<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Category;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class CategoryController extends MainAdminController
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
            $list = Category::where("category_name", "LIKE","%$keyword%")->orderBy('category_name')->paginate(10);

            $list->appends(\Request::only('s'))->links();
        }         
        else
        {
            $list = Category::orderBy('id','DESC')->paginate(10);

        }

        $page_title=trans('words.categories_text');
         
        return view('admin.pages.category.list',compact('page_title','list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.add_category');
         
          return view('admin.pages.category.addedit',compact('page_title'));
        
    }

    public function edit($page_id)    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.edit_category');

          $info = Category::findOrFail($page_id);
        
          return view('admin.pages.category.addedit',compact('page_title','info'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
        
        if(!empty($inputs['id'])){
                
                $rule=array(
                'category_name' => 'required',
                  );
        }else
        {
            $rule=array(
                'category_name' => 'required',                                   
                 );
        }

        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        $inputs = $request->all();
        
        if(!empty($inputs['id'])){
           
            $data_obj = Category::findOrFail($inputs['id']);

        }else{

            $data_obj = new Category;

        }
         
 
         $data_obj->category_name = addslashes($inputs['category_name']);
         $data_obj->category_image = $inputs['category_image'];       

         $data_obj->status = $inputs['status']; 

         if (\Schema::hasColumn('categories', 'category_color') && empty($data_obj->category_color)) {
             $palette = ['#4a7dff','#16b981','#ff9f43','#7c5cff','#0eb5c6','#ff5e93',
                         '#f45b5b','#5b6ef4','#e6a817','#0ea5e9','#8b5cf6','#ef4444',
                         '#14b8a6','#f97316','#6366f1','#db2777'];
             $data_obj->category_color = $palette[array_rand($palette)];
         }
         
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
            
             $data_obj = Category::findOrFail($post_id);
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
