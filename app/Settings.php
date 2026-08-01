<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = ['app_name','app_email', 'app_logo', 'app_company', 'app_website','app_contact','app_version'];

 
	
	 public $timestamps = false;
    
}
