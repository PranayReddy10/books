<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    protected $table = 'books';

    protected $fillable = ['type','content_type','title','image','url'];
 
	
    public $timestamps = false;

	public function departments()
	{
		return $this->belongsToMany('App\Department', 'book_department', 'book_id', 'department_id');
	}

	/**
	 * Resolve a display cover for this row. Videos with no uploaded image fall
	 * back to the YouTube thumbnail automatically.
	 */
	public function coverUrl()
	{
		if (!empty($this->image) && $this->image != 'upload/book_placeholder.jpg') {
			return book_asset_url($this->image);
		}
		if ($this->content_type == 'video' && $this->url_type == 'youtube') {
			$t = youtube_thumb($this->url);
			if ($t) { return $t; }
		}
		return book_asset_url($this->image);
	}

	public function isVideo()
	{
		return $this->content_type == 'video';
	}

	public static function getBookInfo($id,$field_name) 
    { 
		$info = Books::where('id',$id)->first();
		
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
