<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultSubject extends Model
{
    protected $table = 'result_subjects';

    protected $fillable = [
        'result_semester_id', 'subject_code', 'subject_name',
        'internal', 'external', 'total', 'grade', 'credits',
        'grade_points', 'is_backlog',
    ];

    public function semester()
    {
        return $this->belongsTo(ResultSemester::class, 'result_semester_id');
    }
}
