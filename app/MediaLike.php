<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaLike extends Model
{
    protected $table = 'media_likes';

    protected $fillable = ['post_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo('App\MediaPost', 'post_id');
    }
}
