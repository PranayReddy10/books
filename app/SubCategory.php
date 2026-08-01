<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_categories';

    protected $fillable = ['sub_category_name','sub_category_image'];
 
	
    public $timestamps = false;


	public static function getSubCategoryInfo($id,$field_name) 
    { 
		$info = SubCategory::where('status','1')->where('id',$id)->first();
		
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
