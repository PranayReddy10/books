<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Suggestion;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class SuggestionController extends MainAdminController
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
            $list = Suggestion::where("title", "LIKE","%$keyword%")->Orwhere("message", "LIKE","%$keyword%")->orderBy('title')->paginate(10);

            $list->appends(\Request::only('s'))->links();
        }         
        else
        { 
            $list = Suggestion::orderBy('id','DESC')->paginate(10);
        }

        $page_title=trans('words.suggestions');
         
        return view('admin.pages.suggestion.list',compact('page_title','list'));
    }

     
    public function delete($post_id)
    {
        if(Auth::User()->usertype=="Admin" OR Auth::User()->usertype=="Sub_Admin")
        {
            
             $data_obj = Suggestion::findOrFail($post_id);
             $data_obj->delete();

             \Session::flash('flash_message', trans('words.deleted'));
             return redirect()->back();
             
            
        }
        else
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');            
        
        }
    }
 
}
