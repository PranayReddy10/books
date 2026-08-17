<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaPost extends Model
{
    protected $table = 'media_posts';

    protected $fillable = [
        'user_id', 'media_type', 'title', 'description',
        'file_url', 'thumb_url', 'extra_images', 'link_url', 'book_id', 'is_admin_upload',
        'show_views', 'allow_likes', 'allow_comments',
        'upload_status', 'reject_reason', 'status', 'view_count',
    ];

    /**
     * Every image on this post, cover first. Text posts return an empty list and
     * video posts return just their poster's underlying file, if any.
     */
    public function allImages()
    {
        $out = array();
        if ($this->file_url) {
            $out[] = $this->file_url;
        }
        if (!empty($this->extra_images)) {
            $extra = json_decode($this->extra_images, true);
            if (is_array($extra)) {
                foreach ($extra as $url) {
                    if (is_string($url) && $url !== '') {
                        $out[] = $url;
                    }
                }
            }
        }
        return $out;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany('App\MediaLike', 'post_id');
    }

    public function comments()
    {
        return $this->hasMany('App\MediaComment', 'post_id');
    }

    public function likesCount()
    {
        return MediaLike::where('post_id', $this->id)->count();
    }

    public function commentsCount()
    {
        return MediaComment::where('post_id', $this->id)->where('status', 1)->count();
    }

    /**
     * Display name for the uploader shown under each post in the feed.
     * Admin uploads fall back to the app name / "Admin".
     */
    public function uploaderName()
    {
        if ($this->is_admin_upload || empty($this->user_id)) {
            return getcong('app_name') ?: 'Admin';
        }
        $user = User::find($this->user_id);
        return $user ? stripslashes($user->name) : 'User';
    }
}
