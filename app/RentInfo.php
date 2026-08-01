<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentInfo extends Model
{
    protected $table = 'rent_info';

    protected $fillable = ['user_id', 'rent_id'];


	public $timestamps = false;  
	
	
	public static function getRentInfo($rent_id,$user_id,$field_name) 
    { 
    	$rent_info = RentInfo::where('rent_id',$rent_id)->where('user_id',$user_id)->first();
		
		if($rent_info)
		{
			return  $rent_info->$field_name;
		}
		else
		{
			return  '';
		}
		
	}
	
}
