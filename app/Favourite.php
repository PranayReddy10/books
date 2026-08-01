<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $table = 'favourite';

    protected $fillable = ['user_id', 'post_id'];


	public $timestamps = false;  
	
}
