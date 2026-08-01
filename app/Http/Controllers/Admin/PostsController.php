<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\MediaPost;
use App\MediaLike;
use App\MediaComment;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;

class PostsController extends MainAdminController
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

    /**
     * The rich "Posts" grid: all approved posts with stats + toggles.
     * (The pending/rejected moderation queue lives in MediaPostsController.)
     */
    public function list()
    {
        if ($r = $this->guard()) return $r;

        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $source = isset($_GET['source']) ? $_GET['source'] : '';

        $query = MediaPost::where('upload_status', 'approved');
        if ($q != '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }
        if (in_array($type, ['photo', 'video'])) {
            $query->where('media_type', $type);
        }
        if ($source == 'admin') {
            $query->where('is_admin_upload', 1);
        } elseif ($source == 'user') {
            $query->where('is_admin_upload', 0);
        }

        $list = $query->orderBy('id', 'DESC')->paginate(16);

        $page_title = 'Posts';
        return view('admin.pages.posts.list', compact('page_title', 'list', 'q', 'type', 'source'));
    }

    public function edit($id)
    {
        if ($r = $this->guard()) return $r;

        $info = MediaPost::findOrFail($id);
        $page_title = 'Edit Post';
        return view('admin.pages.posts.edit', compact('page_title', 'info'));
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $post->title       = addslashes($request->input('title', ''));
        $post->description = addslashes($request->input('description', ''));
        $post->link_url    = $request->input('link_url', '') ?: null;
        $post->save();

        \Session::flash('flash_message', 'Post updated.');
        return redirect('admin/posts');
    }

    /**
     * Toggle any of the per-post switches: publish (status), views, likes, comments.
     * Called from the card grid (AJAX or plain GET).
     */
    public function toggle($id, $field)
    {
        if ($r = $this->guard()) return $r;

        $allowed = ['status', 'show_views', 'allow_likes', 'allow_comments'];
        if (!in_array($field, $allowed)) {
            return response()->json(['success' => 0, 'msg' => 'Invalid field']);
        }

        $post = MediaPost::findOrFail($id);
        $post->{$field} = $post->{$field} ? 0 : 1;
        $post->save();

        if (request()->ajax()) {
            return response()->json(['success' => 1, 'value' => $post->{$field}]);
        }
        \Session::flash('flash_message', 'Updated.');
        return redirect()->back();
    }

    public function delete($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        // clean up interactions
        MediaLike::where('post_id', $id)->delete();
        MediaComment::where('post_id', $id)->delete();
        \App\MediaNotification::where('post_id', $id)->delete();
        $post->delete();

        \Session::flash('flash_message', trans('words.deleted'));
        return redirect()->back();
    }

    /**
     * Send a push notification for this post to all users. Stores an in-app
     * notification row (broadcast) and fires OneSignal. Tapping opens the post.
     */
    public function notify($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);

        $title = stripslashes($post->title) ?: 'New post';
        $message = 'Check out this new ' . $post->media_type . '!';
        $image = $post->media_type == 'video' ? ($post->thumb_url ?: '') : $post->file_url;

        // in-app broadcast row (user_id null = everyone)
        $note = new \App\MediaNotification;
        $note->post_id = $post->id;
        $note->title = addslashes($title);
        $note->message = addslashes($message);
        $note->image = $image ?: null;
        $note->user_id = null;
        $note->save();

        // OneSignal push (best effort)
        send_media_notification($post->id, $title, $message, $image);

        \Session::flash('flash_message', 'Notification sent for this post.');
        return redirect()->back();
    }

    /**
     * View + moderate the comments on a single post.
     */
    public function comments($id)
    {
        if ($r = $this->guard()) return $r;

        $post = MediaPost::findOrFail($id);
        $comments = MediaComment::where('post_id', $id)->orderBy('id', 'DESC')->paginate(20);

        $page_title = 'Post Comments';
        return view('admin.pages.posts.comments', compact('page_title', 'post', 'comments'));
    }

    public function comment_toggle($id)
    {
        if ($r = $this->guard()) return $r;

        $c = MediaComment::findOrFail($id);
        $c->status = $c->status ? 0 : 1;
        $c->save();

        \Session::flash('flash_message', 'Comment ' . ($c->status ? 'shown' : 'hidden') . '.');
        return redirect()->back();
    }

    public function comment_delete($id)
    {
        if ($r = $this->guard()) return $r;

        MediaComment::where('id', $id)->delete();
        \Session::flash('flash_message', trans('words.deleted'));
        return redirect()->back();
    }
}
