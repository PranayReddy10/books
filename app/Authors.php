<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    protected $table = 'authors';

    protected $fillable = ['name'];
 
	
    public $timestamps = false;


	public static function getAuthorsInfo($id,$field_name) 
    { 
		$info = Authors::where('id',$id)->first();
		
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
