<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
 

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image; 

use Session;
 
 
class IndexController extends Controller
{   
 	  
    public function index()
    {   

        if(!alreadyInstalled()){
            return redirect('public/install');
        }

        if (Auth::check())
        {
                        
            return redirect('admin/dashboard'); 
        }
        else
        {
            return redirect('admin'); 
        }              
         
    }


}
