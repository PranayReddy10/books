<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaNotification extends Model
{
    protected $table = 'media_notifications';

    protected $fillable = ['post_id', 'title', 'message', 'image', 'user_id', 'is_read'];

    public function post()
    {
        return $this->belongsTo('App\MediaPost', 'post_id');
    }
}
