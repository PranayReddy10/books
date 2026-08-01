<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $table = 'reports';

    protected $fillable = ['user_id','post_id','message'];
 
	
    public $timestamps = false;

}
