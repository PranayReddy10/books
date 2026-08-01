<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    protected $table = 'universities';

    protected $fillable = ['university_name', 'status'];

    public static function getUniversityInfo($id, $field_name)
    {
        $info = University::where('id', $id)->first();
        return $info ? $info->$field_name : '';
    }
}
