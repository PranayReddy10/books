<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostRatings extends Model
{
    protected $table = 'post_ratings';

    protected $fillable = ['user_id', 'post_id','rate'];


	public $timestamps = false;  


	public static function getRatingsInfo($id,$field_name) 
    { 
		$info = PostRatings::where('id',$id)->first();
		
		if($info)
		{
			return  $info->$field_name;
		}
		else
		{
			return  '';
		}
	}

	public static function getPostTotalRatings($post_id,$post_type) 
    { 
    	$total = PostRatings::where('post_type',$post_type)->where('post_id',$post_id)->avg('rate');
		 
		return round($total);
	}

	public static function getUserRatings($post_id,$post_type,$user_id=null) 
    { 	
    	if($user_id)
    	{
    		$rate_obj = PostRatings::where('post_type',$post_type)->where('post_id',$post_id)->where('user_id',$user_id)->first();
		 	
    		if($rate_obj)
    		{
    			return $rate_obj->rate;
    		}
    		else
    		{
    			return 0;
    		}

			
    	}
    	else
    	{ 
		 
			return 0;
    	}
 
	}
 
}
