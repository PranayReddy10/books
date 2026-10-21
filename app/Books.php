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
	 * Books a student of this university may see: books tagged with one of the
	 * university's departments, plus books that are not tied to any university
	 * (no department at all, or only departments without a university). A null
	 * university leaves the query alone.
	 */
	public function scopeForUniversity($query, $universityId)
	{
		if (empty($universityId)) {
			return $query;
		}
		$universityId = (int) $universityId;

		return $query->whereNotExists(function ($other) use ($universityId) {
			// Hidden only when every department it is tagged with belongs to
			// some other university.
			$other->selectRaw('1')
				->from('book_department as bd')
				->join('departments as d', 'd.id', '=', 'bd.department_id')
				->whereColumn('bd.book_id', 'books.id')
				->whereNotNull('d.university_id')
				->where('d.university_id', '!=', $universityId)
				->whereNotExists(function ($own) use ($universityId) {
					$own->selectRaw('1')
						->from('book_department as bd2')
						->join('departments as d2', 'd2.id', '=', 'bd2.department_id')
						->whereColumn('bd2.book_id', 'books.id')
						->where(function ($q) use ($universityId) {
							$q->where('d2.university_id', $universityId)
								->orWhereNull('d2.university_id');
						});
				});
		});
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
