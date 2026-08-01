<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\PostRatings;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class ReviewsController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
          
    }

    public function list()
    { 
 
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];  
            $list = PostRatings::where("review_text", "LIKE","%$keyword%")->orderBy('review_text')->paginate(10);

            $list->appends(\Request::only('s'))->links();
        }         
        else
        { 
            $list = PostRatings::orderBy('id','DESC')->paginate(10);
        }
        
        $page_title=trans('words.reviews');
         
        return view('admin.pages.reviews.list',compact('page_title','list'));
    }
 
    
}
