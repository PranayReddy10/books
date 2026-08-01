<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = ['university_id', 'department_name', 'status'];

    public function university()
    {
        return $this->belongsTo('App\University', 'university_id');
    }

    public static function getDepartmentInfo($id, $field_name)
    {
        $info = Department::where('id', $id)->first();

        if ($info) {
            return $info->$field_name;
        } else {
            return '';
        }
    }
}
