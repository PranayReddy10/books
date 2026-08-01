<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Reports;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class ReportsController extends MainAdminController
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
            $list = Reports::where("message", "LIKE","%$keyword%")->orderBy('message')->paginate(10);

            $list->appends(\Request::only('s'))->links();
        }         
        else
        { 
            $list = Reports::orderBy('id','DESC')->paginate(10);
        }
        
        $page_title=trans('words.reports');
         
        return view('admin.pages.reports.list',compact('page_title','list'));
    }

     
    public function delete($post_id)
    {
        if(Auth::User()->usertype=="Admin" OR Auth::User()->usertype=="Sub_Admin")
        {
            
             $data_obj = Reports::findOrFail($post_id);
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
