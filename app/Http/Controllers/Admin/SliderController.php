<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Slider;
use App\Songs;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class SliderController extends MainAdminController
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

        $list = Slider::orderBy('id','DESC')->paginate(10);

        $page_title=trans('words.slider');
         
        return view('admin.pages.slider.list',compact('page_title','list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.add_slider');

          $songs_list = Songs::orderBy('id')->get();
         
          return view('admin.pages.slider.addedit',compact('page_title','songs_list'));
        
    }

    public function edit($page_id)    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.edit_slider');

          $info = Slider::findOrFail($page_id);

          $songs_list = Songs::orderBy('id')->get();
        
          return view('admin.pages.slider.addedit',compact('page_title','info','songs_list'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
        
        if(!empty($inputs['id'])){
                
                $rule=array(
                'slider_title' => 'required'
                  );
        }
        else
        {
            $rule=array(
                'slider_title' => 'required',                            
                 );
        }
        
        
        $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        $inputs = $request->all();
        
        if(!empty($inputs['id'])){
           
            $data_obj = Slider::findOrFail($inputs['id']);

        }else{

            $data_obj = new Slider;

        }
         
 
         $data_obj->slider_title = addslashes($inputs['slider_title']);
         $data_obj->slider_image = $inputs['slider_image'];
         $data_obj->slider_info = addslashes($inputs['slider_info']);        
         $data_obj->songs_ids = implode(',', $inputs['songs_ids']);
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

    public function delete($post_id)
    {
        if(Auth::User()->usertype=="Admin" OR Auth::User()->usertype=="Sub_Admin")
        {
            
             $data_obj = Slider::findOrFail($post_id);
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
