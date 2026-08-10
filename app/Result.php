<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'hall_ticket_no', 'user_id', 'university_id', 'student_name',
        'regulation', 'degree', 'branch', 'current_cgpa', 'total_credits',
        'backlogs_count', 'source', 'verified', 'locked', 'is_public', 'share_token',
    ];

    public function semesters()
    {
        return $this->hasMany(ResultSemester::class, 'result_id')->orderBy('sem_code', 'asc');
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class, 'result_id');
    }
}
