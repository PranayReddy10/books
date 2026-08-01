<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Category;
use App\SubCategory;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class SubCategoryController extends MainAdminController
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
            $list = SubCategory::where("sub_category_name", "LIKE","%$keyword%")->orderBy('sub_category_name')->paginate(12);

            $list->appends(\Request::only('s'))->links();
        }
        else if(isset($_GET['cat_id']))
        {
            $cat_id = $_GET['cat_id'];  
            $list = SubCategory::where("cat_id", $cat_id)->orderBy('sub_category_name')->paginate(12);

            $list->appends(\Request::only('cat_id'))->links();
        }         
        else
        {
            $list = SubCategory::orderBy('id','DESC')->paginate(12);

        }

        $page_title=trans('words.sub_categories_text');

        $cat_list = Category::orderBy('category_name')->get();
         
        return view('admin.pages.subcategory.list',compact('page_title','list','cat_list'));
    }

    public function add()    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.add_sub_category');

          $cat_list = Category::orderBy('category_name')->get();
         
          return view('admin.pages.subcategory.addedit',compact('page_title','cat_list'));
        
    }

    public function edit($page_id)    
    {     
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
            {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');
                
             }  

          $page_title=trans('words.edit_sub_category');

          $info = SubCategory::findOrFail($page_id);

          $cat_list = Category::orderBy('category_name')->get();
        
          return view('admin.pages.subcategory.addedit',compact('page_title','info','cat_list'));
        
    }

    public function addnew(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
        
        if(!empty($inputs['id'])){
                
                $rule=array(
                    'category' => 'required',
                    'sub_category_name' => 'required',
                  );
        }else
        {
            $rule=array(
                'category' => 'required',
                'sub_category_name' => 'required',                                   
                 );
        }

        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        $inputs = $request->all();
        
        if(!empty($inputs['id'])){
           
            $data_obj = SubCategory::findOrFail($inputs['id']);

        }else{

            $data_obj = new SubCategory;

        }         
         
         $data_obj->cat_id = $inputs['category']; 
         $data_obj->sub_category_name = addslashes($inputs['sub_category_name']);
         $data_obj->sub_category_image = $inputs['sub_category_image'];       

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
            
             $data_obj = SubCategory::findOrFail($post_id);
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

    public function ajax_get_sub_cat($cat_id){ 
        
         
        $sub_cat_list = SubCategory::where('cat_id',$cat_id)->orderBy('id','DESC')->get(); 

        //dd($sub_cat_list);exit;
         
        return view('admin.pages.subcategory.ajax_sub_cat_list',compact('sub_cat_list'));
    }
 
}
