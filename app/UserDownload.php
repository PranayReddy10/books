<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDownload extends Model
{
    protected $table = 'user_downloaded';

    protected $fillable = ['user_id', 'post_id'];


	public $timestamps = false;  
  
}
