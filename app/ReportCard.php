<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    protected $table = 'report_cards';

    protected $fillable = [
        'result_id', 'pdf_url', 'verified_at_generation', 'generated_at',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class, 'result_id');
    }
}
