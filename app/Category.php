<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['category_name','category_image','category_color','status'];
 
	
    public $timestamps = false;


	public static function getCategoryInfo($id,$field_name) 
    { 
		$info = Category::where('status','1')->where('id',$id)->first();
		
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
