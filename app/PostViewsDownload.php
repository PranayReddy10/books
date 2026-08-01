<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostViewsDownload extends Model
{
    protected $table = 'post_views';

    protected $fillable = ['user_id', 'post_id','post_type'];


	public $timestamps = false;  


	public static function getPostTotalViews($post_id,$post_type) 
    { 
    	$total = PostViewsDownload::where('post_id',$post_id)->where('post_type',$post_type)->sum('post_views');
		 
		return $total;
	}

	public static function getPostTotalDownload($post_id,$post_type) 
    { 
    	$total = PostViewsDownload::where('post_id',$post_id)->where('post_type',$post_type)->sum('post_download');
		 
		return $total;
	}

	 
 
}
