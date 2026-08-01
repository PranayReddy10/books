<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $table = 'analytics';

    protected $fillable = ['country', 'operating_system'];


	public $timestamps = false;  


	public static function getPostTotalViews($post_id,$post_type) 
    { 
    	$total = Analytics::where('post_id',$post_id)->where('post_type',$post_type)->sum('post_views');
		 
		return $total;
	}
 
}
