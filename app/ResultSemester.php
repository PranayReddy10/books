<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultSemester extends Model
{
    protected $table = 'result_semesters';

    protected $fillable = [
        'result_id', 'sem_code', 'sgpa', 'credits_earned', 'exam_month_year',
        'verified', 'locked',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class, 'result_id');
    }

    public function subjects()
    {
        return $this->hasMany(ResultSubject::class, 'result_semester_id');
    }
}
