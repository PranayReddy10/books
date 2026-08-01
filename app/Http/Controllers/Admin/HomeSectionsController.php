<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\HomeSections;
use App\Category;
use App\SubCategory;
use App\Authors;
use App\Books;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class HomeSectionsController extends MainAdminController
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

        $list = HomeSections::orderBy('id','DESC')->paginate(10);

        $page_title=trans('words.home_sections');
         
        return view('admin.pages.home_sections.list',compact('page_title','list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
            }  

          $page_title=trans('words.add_section');

          $category_list = Category::orderBy('id')->get();
          $sub_cat_list = SubCategory::orderBy('id')->get();
          $authors_list = Authors::orderBy('id')->get();
          $books_list = Books::orderBy('id')->get();
          
         
          return view('admin.pages.home_sections.addedit',compact('page_title','category_list','sub_cat_list','authors_list','books_list'));
        
    }

    public function edit($page_id)    
    {     
           if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
            }  

          $page_title=trans('words.edit_Section');

          $info = HomeSections::findOrFail($page_id);

          $category_list = Category::orderBy('id')->get();
          $sub_cat_list = SubCategory::orderBy('id')->get();
          $authors_list = Authors::orderBy('id')->get();
          $books_list = Books::orderBy('id')->get();
        
          return view('admin.pages.home_sections.addedit',compact('page_title','info','category_list','sub_cat_list','authors_list','books_list'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
        
       $inputs = $request->all();


        if(!empty($inputs['id'])){
                
            if($inputs['post_type']=="category")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'category' => 'required'
                  );
            }
            else if($inputs['post_type']=="subcategory")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'sub_category' => 'required'
                  );
            }
            else if($inputs['post_type']=="author")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'authors' => 'required'
                  );
            }
            else
            {
                $rule=array(
                    'section_name' => 'required',
                    'post_type' => 'required',
                    'books' => 'required'
                      );
            }
        }
        else
        {
            if($inputs['post_type']=="category")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'category' => 'required'
                  );
            }
            else if($inputs['post_type']=="subcategory")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'sub_category' => 'required'
                  );
            }
            else if($inputs['post_type']=="author")
            {
                $rule=array(
                'section_name' => 'required',
                'post_type' => 'required',
                'authors' => 'required'
                  );
            }
            else
            {
                $rule=array(
                    'section_name' => 'required',
                    'post_type' => 'required',
                    'books' => 'required'
                      );
            }
        }
        
        
        $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        
        if(!empty($inputs['id'])){
           
            $data_obj = HomeSections::findOrFail($inputs['id']);

        }else{

            $data_obj = new HomeSections;

        }
 
         $data_obj->section_name = addslashes($inputs['section_name']);
         $data_obj->post_type = $inputs['post_type'];        
         
         if($inputs['post_type']=="category")
         {
            $data_obj->post_ids = implode(',', $inputs['category']);
         } 
         else if($inputs['post_type']=="subcategory")
         {  
            $data_obj->post_ids = implode(',', $inputs['sub_category']);
         }
         else if($inputs['post_type']=="author")
         {  
            $data_obj->post_ids = implode(',', $inputs['authors']);
         }         
         else if($inputs['post_type']=="book")
         {  
            $data_obj->post_ids = implode(',', $inputs['books']);
         }
         else
         {
            $data_obj->post_ids = null;
         }
         
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
            
             $data_obj = HomeSections::findOrFail($post_id);
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
