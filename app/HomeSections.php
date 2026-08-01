<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeSections extends Model
{
    protected $table = 'home_sections';

    protected $fillable = ['section_name','section_type'];
 
	
    public $timestamps = false;


	public static function getCategoryInfo($id,$field_name) 
    { 
		$info = HomeSections::where('status','1')->where('id',$id)->first();
		
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
