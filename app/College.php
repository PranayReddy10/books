<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $table = 'colleges';

    protected $fillable = ['college_name', 'status'];
}
