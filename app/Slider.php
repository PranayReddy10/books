<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'slider';

    protected $fillable = ['slider_title','slider_image'];
 
	
    public $timestamps = false;


	public static function getSliderInfo($id,$field_name) 
    { 
		$info = Slider::where('status','1')->where('id',$id)->first();
		
		if($info)
		{
			return  $info->$field_name;
		}
		else
		{
			return  '';
		}
	}
    
}
