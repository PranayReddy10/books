<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaComment extends Model
{
    protected $table = 'media_comments';

    protected $fillable = ['post_id', 'user_id', 'comment', 'status'];

    public function post()
    {
        return $this->belongsTo('App\MediaPost', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
