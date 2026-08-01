<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\MediaPost;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class MediaPostsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function guard()
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }
        return null;
    }

    public function list()
    {
        if ($r = $this->guard()) return $r;

        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';

        $query = MediaPost::query();
        if (in_array($filter, ['pending', 'approved', 'rejected'])) {
            $query->where('upload_status', $filter);
        }
        $list = $query->orderBy('id', 'DESC')->paginate(15);

        $pending_count = MediaPost::where('upload_status', 'pending')->count();

        $page_title = 'Media Feed (Photos & Videos)';

        return view('admin.pages.media.list', compact('page_title', 'list', 'filter', 'pending_count'));
    }

    // Admin add form
    public function add()
    {
        if ($r = $this->guard()) return $r;
        $page_title = 'Add Media Post';
        $books_list = \App\Books::where('status', 1)->orderBy('title', 'ASC')->get(['id', 'title']);
        return view('admin.pages.media.add', compact('page_title', 'books_list'));
    }

    // Admin upload (always auto-approved & live)
    public function store(Request $request)
    {
        if ($r = $this->guard()) return $r;

        $media_type = $request->input('media_type', 'photo');
        $title      = $request->input('title', '');
        $description = $request->input('description', '');

        $file = $request->file('media_file');
        if (!$file) {
            \Session::flash('flash_message', 'Please choose a file to upload.');
            return redirect()->back();
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExts = ['mp4', 'mov', 'webm', 'm4v'];

        if ($media_type == 'photo' && !in_array($ext, $imageExts)) {
            \Session::flash('flash_message', 'Invalid image type. Allowed: jpg, png, gif, webp.');
            return redirect()->back();
        }
        if ($media_type == 'video' && !in_array($ext, $videoExts)) {
            \Session::flash('flash_message', 'Invalid video type. Allowed: mp4, mov, webm.');
            return redirect()->back();
        }

        $fileName = Str::slug(substr($title ?: 'media', 0, 40), '-') . '-' . md5(uniqid('', true)) . '.' . $ext;
        $file_url = spaces_upload($file, 'media/' . $media_type . 's', $fileName);

        // Optional separate thumbnail (mainly for videos)
        $thumb_url = null;
        if ($request->hasFile('thumb_file')) {
            $thumb = $request->file('thumb_file');
            $textt = strtolower($thumb->getClientOriginalExtension());
            if (in_array($textt, $imageExts)) {
                $tName = 'thumb-' . md5(uniqid('', true)) . '.' . $textt;
                $thumb_url = spaces_upload($thumb, 'media/thumbs', $tName);
            }
        }

        $post = new MediaPost;
        $post->user_id        = null;
        $post->media_type     = $media_type;
        $post->title          = addslashes($title);
        $post->description    = addslashes($description);
        $post->file_url       = $file_url;
        $post->thumb_url      = $thumb_url;
        $post->link_url       = $request->input('link_url', '') ?: null;
        $post->book_id        = $request->input('book_id') ?: null;
        $post->is_admin_upload = 1;
        $post->upload_status  = 'approved';
        $post->status         = 1;    // live immediately
        $post->save();

        \Session::flash('flash_message', 'Media published.');
        return redirect('admin/media?filter=approved');
    }

    // Full detail + edit page for a single media item.
    public function edit($id)
    {
        if ($r = $this->guard()) return $r;

        $info = MediaPost::findOrFail($id);
        $like_count = \App\MediaLike::where('post_id', $id)->count();
        $comment_count = \App\MediaComment::where('post_id', $id)->where('status', 1)->count();
        $books_list = \App\Books::where('status', 1)->orderBy('title', 'ASC')->get(['id', 'title']);

        $page_title = 'Media Details';
        return view('admin.pages.media.edit', compact('page_title', 'info', 'like_count', 'comment_count', 'books_list'));
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->title       = addslashes($request->input('title', ''));
        $post->description = addslashes($request->input('description', ''));
        $post->link_url    = $request->input('link_url', '') ?: null;
        $post->book_id     = $request->input('book_id') ?: null;
        $post->save();

        \Session::flash('flash_message', 'Media updated.');
        return redirect('admin/media/edit/' . $id);
    }

    // Enable/disable (publish) toggle from the details page or list.
    public function toggle_status($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->status = $post->status ? 0 : 1;
        $post->save();

        if (request()->ajax()) {
            return response()->json(['success' => 1, 'value' => $post->status]);
        }
        \Session::flash('flash_message', $post->status ? 'Post enabled.' : 'Post disabled.');
        return redirect()->back();
    }

    public function approve($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->upload_status = 'approved';
        $post->reject_reason = null;
        $post->status = 1;
        $post->save();

        \Session::flash('flash_message', 'Media approved and published.');
        return redirect()->back();
    }

    public function reject(Request $request, $id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->upload_status = 'rejected';
        $post->reject_reason = $request->input('reject_reason', 'Not approved');
        $post->status = 0;
        $post->save();

        \Session::flash('flash_message', 'Media rejected.');
        return redirect()->back();
    }

    public function delete($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->delete();

        \Session::flash('flash_message', trans('words.deleted'));
        return redirect()->back();
    }
}