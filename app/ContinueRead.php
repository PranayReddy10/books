<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContinueRead extends Model
{
    protected $table = 'continue_read';

    protected $fillable = ['user_id', 'post_id','page_num'];


	public $timestamps = false;  
 
	public static function getContinueRead($post_id,$post_type,$user_id=null) 
    { 	
    	if($user_id)
    	{
    		$read_obj = ContinueRead::where('post_id',$post_id)->where('user_id',$user_id)->first();
		 	
    		if($read_obj)
    		{
    			return $read_obj->page_num;
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
