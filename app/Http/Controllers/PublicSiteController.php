<?php

namespace App\Http\Controllers;

use App\Books;
use App\Category;
use App\SubCategory;
use App\MediaPost;
use App\MediaComment;
use App\MediaLike;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicSiteController extends Controller
{
    /* Home: 3D hero, a couple of featured books, feed teaser. */
    public function home()
    {
        $featured_books = Books::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('upload_status')->orWhere('upload_status', 'approved');
            })
            ->where('featured', 1)
            ->orderBy('id', 'DESC')
            ->take(6)
            ->get();

        // fallback: if none flagged featured, show the latest few
        if ($featured_books->count() == 0) {
            $featured_books = Books::where('status', 1)
                ->where(function ($q) {
                    $q->whereNull('upload_status')->orWhere('upload_status', 'approved');
                })
                ->orderBy('id', 'DESC')->take(6)->get();
        }

        $feed_teaser = MediaPost::where('status', 1)->where('upload_status', 'approved')
            ->orderBy('id', 'DESC')->take(6)->get();

        $categories = Category::where('status', 1)->orderBy('category_name', 'ASC')->take(12)->get();

        return view('public.home', compact('featured_books', 'feed_teaser', 'categories'));
    }

    /* Books catalog with category filter + search + pagination. */
    public function books(Request $request)
    {
        $q = trim($request->get('q', ''));
        $cat = $request->get('cat', '');

        $query = Books::where('status', 1)
            ->where(function ($sub) {
                $sub->whereNull('upload_status')->orWhere('upload_status', 'approved');
            });

        if ($q != '') {
            $query->where('title', 'like', '%' . $q . '%');
        }
        if ($cat != '') {
            $query->where('cat_id', $cat);
        }

        $books = $query->orderBy('id', 'DESC')->paginate(18)->appends($request->query());
        $categories = Category::where('status', 1)->orderBy('category_name', 'ASC')->get();

        return view('public.books', compact('books', 'categories', 'q', 'cat'));
    }
    
    public function reportDetail($token)
    {
        $result = \App\Result::where('share_token', $token)->firstOrFail();
        $result->load('semesters');

        $sems = array();
        foreach ($result->semesters as $sem) {
            $sem->loaded_subjects = $sem->subjects()->get();
            $sems[] = $sem;
        }

        $latestCard = \App\ReportCard::where('result_id', $result->id)->orderBy('id', 'DESC')->first();
        $image_url  = $latestCard ? $latestCard->pdf_url : '';

        return view('public.report_detail', compact('result', 'sems', 'image_url'));
    }

    /* Book detail (public). Reading/downloading requires login (gated in view). */
    public function bookDetail($slug)
    {
        $id = (int) Str::before($slug, '-');
        $book = Books::where('id', $id)->where('status', 1)->firstOrFail();

        // Count this view the same way the app does.
        if (function_exists('post_views_save')) {
            post_views_save($book->id, 'Book');
        }

        $related = Books::where('status', 1)
            ->where('cat_id', $book->cat_id)
            ->where('id', '!=', $book->id)
            ->where(function ($q) {
                $q->whereNull('upload_status')->orWhere('upload_status', 'approved');
            })
            ->orderBy('id', 'DESC')->take(6)->get();

        return view('public.book_detail', compact('book', 'related'));
    }

    /* The scrollable photo/video feed (public browse). */
    public function feed(Request $request)
    {
        $type = $request->get('type', '');
        $query = MediaPost::where('status', 1)->where('upload_status', 'approved');
        if (in_array($type, ['photo', 'video'])) {
            $query->where('media_type', $type);
        }
        $posts = $query->orderBy('id', 'DESC')->paginate(12)->appends($request->query());

        return view('public.feed', compact('posts', 'type'));
    }

    /* Single post permalink (shareable, SEO + opened from notifications). */
    public function postDetail($id)
    {
        $post = MediaPost::where('id', $id)->where('status', 1)->where('upload_status', 'approved')->firstOrFail();

        // Count this view (same counter the app increments via media_view).
        $post->increment('view_count');

        $comments = MediaComment::where('post_id', $id)->where('status', 1)->orderBy('id', 'DESC')->take(30)->get();
        $like_count = MediaLike::where('post_id', $id)->count();

        return view('public.post_detail', compact('post', 'comments', 'like_count'));
    }

    /* ---------------- Account (logged-in user) ---------------- */

    public function account()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // This user's own uploaded posts + books
        $my_posts = MediaPost::where('user_id', $user->id)->orderBy('id', 'DESC')->take(12)->get();
        $my_books = Books::where('uploaded_by', $user->id)->orderBy('id', 'DESC')->take(12)->get();

        return view('public.account', compact('user', 'my_posts', 'my_books'));
    }

    /* Reader: gated behind login. Resolves the book's file like the app does. */
    public function read($slug)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $id = (int) Str::before($slug, '-');
        $book = Books::where('id', $id)->where('status', 1)->firstOrFail();

        // Resolve file URL the same way the API does.
        if ($book->url_type == 'server_url') {
            $file_url = $book->url;
        } else {
            $file_url = url('/' . ltrim($book->url, '/'));
        }

        $ext = strtolower(pathinfo(parse_url($file_url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return view('public.reader', compact('book', 'file_url', 'ext'));
    }

    /* ---------------- SEO ---------------- */

    public function sitemap()
    {
        $books = Books::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('upload_status')->orWhere('upload_status', 'approved');
            })->orderBy('id', 'DESC')->get();

        $posts = MediaPost::where('status', 1)->where('upload_status', 'approved')->orderBy('id', 'DESC')->get();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Force the public host so <loc> URLs match the sitemap's own domain,
        // regardless of APP_URL. A sitemap must list same-domain URLs only.
        $base = 'https://' . request()->getHost();

        $enc = function ($s) {
            $flags = defined('ENT_XML1') ? ENT_XML1 : ENT_QUOTES;
            return htmlspecialchars($s, $flags, 'UTF-8');
        };

        $add = function ($path, $freq, $pri) use ($base, $enc) {
            $loc = $base . '/' . ltrim($path, '/');
            return '  <url><loc>' . $enc($loc)
                 . '</loc><changefreq>' . $freq . '</changefreq><priority>' . $pri . "</priority></url>\n";
        };

        $xml .= $add('', 'daily', '1.0');
        $xml .= $add('books', 'daily', '0.9');
        $xml .= $add('feed', 'daily', '0.9');

        foreach ($books as $b) {
            $xml .= $add('book/' . $b->id . '-' . Str::slug($b->title), 'weekly', '0.7');
        }
        foreach ($posts as $p) {
            $xml .= $add('post/' . $p->id, 'weekly', '0.6');
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $base = 'https://' . request()->getHost();
        $lines = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /signin\n\nSitemap: " . $base . "/sitemap.xml\n";
        return response($lines, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Android App Links verification file.
     * Served at /.well-known/assetlinks.json so tapping an https link to this
     * site opens the app (once the app ships intent filters + this fingerprint).
     * Replace the sha256 value with your app's signing cert fingerprint
     * (Play Console -> App integrity -> App signing, SHA-256).
     */
    public function assetlinks()
    {
        $fingerprints = config('app.android_sha256', []);
        // Fallback placeholder so the endpoint exists; replace via config/env.
        if (empty($fingerprints)) {
            $fingerprints = ['REPLACE_WITH_YOUR_APP_SHA256_FINGERPRINT'];
        }

        $data = [[
            'relation' => ['delegate_permission/common.handle_all_urls'],
            'target'   => [
                'namespace'                => 'android_app',
                'package_name'             => 'com.jntuh.books',
                'sha256_cert_fingerprints' => array_values((array) $fingerprints),
            ],
        ]];

        return response()->json($data)->header('Content-Type', 'application/json');
    }
}