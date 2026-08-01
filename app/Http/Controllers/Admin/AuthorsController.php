<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Authors;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class AuthorsController extends MainAdminController
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
            $list = Authors::where("name", "LIKE","%$keyword%")->orderBy('name')->paginate(8);

            $list->appends(\Request::only('s'))->links();
        }         
        else
        {
            $list = Authors::orderBy('id','DESC')->paginate(8);

        }

        $page_title=trans('words.authors_text');
         
        return view('admin.pages.authors.list',compact('page_title','list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.add_author');
         
          return view('admin.pages.authors.addedit',compact('page_title'));
        
    }

    public function edit($page_id)    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.edit_author');

          $info = Authors::findOrFail($page_id);
        
          return view('admin.pages.authors.addedit',compact('page_title','info'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
        
        if(!empty($inputs['id'])){
                
                $rule=array(
                'name' => 'required',
                  );
        }else
        {
            $rule=array(
                'name' => 'required',
                'image' => 'required',                             
                 );
        }

        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        $inputs = $request->all();
        
        if(!empty($inputs['id'])){
           
            $data_obj = Authors::findOrFail($inputs['id']);

        }else{

            $data_obj = new Authors;

        }
         
 
         $data_obj->name = addslashes($inputs['name']);
         $data_obj->info = addslashes($inputs['info']);
         $data_obj->image = $inputs['image'];       
         
         $data_obj->facebook_url = addslashes($inputs['facebook_url']);
         $data_obj->instagram_url = addslashes($inputs['instagram_url']);
         $data_obj->youtube_url = addslashes($inputs['youtube_url']);
         $data_obj->website_url = addslashes($inputs['website_url']);       

         $data_obj->status = $inputs['status']; 
         
         $data_obj->save();
         
        
        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }               
        
        
    }
 
}
