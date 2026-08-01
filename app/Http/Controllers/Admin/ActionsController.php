<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Category;
use App\SubCategory;
use App\Authors;
use App\Books;
use App\Reports;
use App\Pages;
use App\Favourite;
use App\PostRatings;
use App\PostViewsDownload;
use App\SubscriptionPlan;
use App\AppAds;
use App\PaymentGateway;
use App\HomeSections;
use App\ContinueRead;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class ActionsController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
          
    }
 

    public function ajax_status(Request $request)
    {  
       
       //$data =  \Request::except(array('_token'));

        $inputs = $request->all(); 
        //dd($inputs);exit;

        if(is_array($inputs['id']))
        {
            $post_ids=$inputs['id'];
        }
        else
        {
            $post_id=$inputs['id'];
            $value=$inputs['value'];
        }
  
        $action_for=$inputs['action_for'];
        
        if($action_for=="cat_status")
        {

            $data_obj = Category::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 
            }
            else
            {
                $data_obj->status = 0; 
            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }
       else if($action_for=="sub_cat_status")
        {

            $data_obj = SubCategory::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 
            }
            else
            {
                $data_obj->status = 0; 
            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }         
        else if($action_for=="author_status")
        {

            $data_obj = Authors::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 
            }
            else
            {
                $data_obj->status = 0; 
            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }
        else if($action_for=="book_status")
        {

            $data_obj = Books::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 
            }
            else
            {
                $data_obj->status = 0; 

                $continue_obj = ContinueRead::where('post_id',$post_id)->delete();

                //Remove from Home Section
                $homesec_list = HomeSections::where('post_type','book')->get();

                foreach ($homesec_list as $homesect_data) {

                    $home_id=$homesect_data->id;
                    $book_ids=$homesect_data->post_ids;
                    
                    $new_ids=remove_from_string($book_ids,$post_id);

                    $homesect_obj = HomeSections::findOrFail($home_id);

                    $homesect_obj->post_ids =$new_ids;

                    $homesect_obj->save();
                }

            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }
        else if($action_for=="book_enable_multiple")
        {

            foreach($post_ids as $pid)
            { 
                $data_obj = Books::findOrFail($pid);        
     
                $data_obj->status = 1;   
                
                $data_obj->save();  

            }
             
            $response['status'] = 1;
            
        }
        else if($action_for=="book_disable_multiple")
        {

            foreach($post_ids as $pid)
            { 
                $data_obj = Books::findOrFail($pid);        
     
                $data_obj->status = 0;   
                
                $data_obj->save();  

                $continue_obj = ContinueRead::where('post_id',$pid)->delete();

                //Remove from Home Section
                $homesec_list = HomeSections::where('post_type','book')->get();

                foreach ($homesec_list as $homesect_data) {

                    $home_id=$homesect_data->id;
                    $book_ids=$homesect_data->post_ids;
                    
                    $new_ids=remove_from_string($book_ids,$pid);

                    $homesect_obj = HomeSections::findOrFail($home_id);

                    $homesect_obj->post_ids =$new_ids;

                    $homesect_obj->save();
                }

            }
             
            $response['status'] = 1;
            
        }
        else if($action_for=="book_featured")
        {

            $data_obj = Books::findOrFail($post_id);        
     
            if($value=="true")
            { 
                $data_obj->featured = 1; 
                
                $response['set_color'] = "green";
                $response['set_title'] = "Remove Slider";
                $response['re_set_value'] = "false";

                $response['res_msg'] = "Set as slider";
            }
            else
            {
                $data_obj->featured = 0;
                
                $response['set_color'] = "red"; //#e32a3c
                $response['set_title'] = "Set Slider";
                $response['re_set_value'] = "true";

                $response['res_msg'] = "Remove from slider";
            }        
             
            $data_obj->save();             
            
            $response['status'] = 1;
            
        }
        else if($action_for=="ads_status")
        {

            $data_obj = AppAds::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 

                //Other Ads Disable
                AppAds::where('id','!=', $post_id)->update(['status' => 0]);
            }
            else
            {
                $data_obj->status = 0; 
            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }     
        else if($action_for=="payment_status")
        {

            $data_obj = PaymentGateway::findOrFail($post_id);        
     
            if($value=="true")
            {
                $data_obj->status = 1; 
 
            }
            else
            {
                $data_obj->status = 0; 
            }        
             
            $data_obj->save();             
            $response['status'] = 1;
            
        }
        else
        {
            $response['status'] = 0;
        }     

        echo json_encode($response);
        exit;   
    }

    public function ajax_delete(Request $request)
    {  
        
        $inputs = $request->all(); 
        //dd($inputs);exit;

        if(!isset($inputs['id']))
        {
            $response['status'] = 0;           
              
            echo json_encode($response);
            exit;

        }
        
        if(is_array($inputs['id']))
        {
            $post_ids=$inputs['id'];
        }
        else
        {
            $post_id=$inputs['id'];
        }

        //echo $post_id;exit;
         
        //$post_id=$inputs['id'];
        $action_for=$inputs['action_for'];
        
        if($action_for=="cat_delete")
        {
            $data_obj = Category::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }
        else if($action_for=="sub_cat_delete")
        {
            $data_obj = SubCategory::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        } 
        else if($action_for=="author_delete")
        {
            $data_obj = Authors::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }         
        else if($action_for=="book_delete")
        {
            $fav_obj = Favourite::where('post_type','Book')->where('post_id',$post_id)->delete();
            $rate_obj = PostRatings::where('post_type','Book')->where('post_id',$post_id)->delete();
            $rep_obj = Reports::where('post_type','Book')->where('post_id',$post_id)->delete(); 
            
            $view_obj = PostViewsDownload::where('post_type','Book')->where('post_id',$post_id)->delete(); 

            $continue_obj = ContinueRead::where('post_id',$post_id)->delete();

            //Remove from Home Section
            $homesec_list = HomeSections::where('post_type','book')->get();

            foreach ($homesec_list as $homesect_data) {

                $home_id=$homesect_data->id;
                $book_ids=$homesect_data->post_ids;
                
                $new_ids=remove_from_string($book_ids,$post_id);

                $homesect_obj = HomeSections::findOrFail($home_id);

                $homesect_obj->post_ids =$new_ids;

                $homesect_obj->save();
            }

            $data_obj = Books::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        } 
        else if($action_for=="book_delete_selected")
        {
            foreach($post_ids as $pid){

                $fav_obj = Favourite::where('post_type','Book')->where('post_id',$pid)->delete();
                $rate_obj = PostRatings::where('post_type','Book')->where('post_id',$pid)->delete();
                $rep_obj = Reports::where('post_type','Book')->where('post_id',$pid)->delete(); 
                
                $view_obj = PostViewsDownload::where('post_type','Book')->where('post_id',$pid)->delete(); 

                $continue_obj = ContinueRead::where('post_id',$pid)->delete();

                //Remove from Home Section
                $homesec_list = HomeSections::where('post_type','book')->get();

                foreach ($homesec_list as $homesect_data) {

                    $home_id=$homesect_data->id;
                    $book_ids=$homesect_data->post_ids;
                    
                    $new_ids=remove_from_string($book_ids,$pid);

                    $homesect_obj = HomeSections::findOrFail($home_id);

                    $homesect_obj->post_ids =$new_ids;

                    $homesect_obj->save();
                }

                $data_obj = Books::findOrFail($pid);
                $data_obj->delete();

            }
            
            $response['status'] = 1;
        }
        else if($action_for=="home_sec_delete")
        {
            $data_obj = HomeSections::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }
        else if($action_for=="plan_delete")
        {
            $data_obj = SubscriptionPlan::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }
        else if($action_for=="reviews_delete")
        {
            $data_obj = PostRatings::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        } 
        else if($action_for=="report_delete")
        {
            $data_obj = Reports::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }
        else if($action_for=="page_delete")
        {
            $data_obj = Pages::findOrFail($post_id);
            $data_obj->delete(); 
             
            $response['status'] = 1;            
        }
        else if($action_for=="user_delete")
        {
            if($post_id==1)
            { 
                $response['status'] = 0;
            }
            else
            {
                $fav_obj = Favourite::where('user_id',$post_id)->delete();
                $rate_obj = PostRatings::where('user_id',$post_id)->delete();
                $rep_obj = Reports::where('user_id',$post_id)->delete();

                $data_obj = User::findOrFail($post_id);
                $data_obj->delete(); 
             
                $response['status'] = 1;     
            }
                   
        }   
        else
        {
            $response['status'] = 0;            
        }     

        echo json_encode($response);
        exit;    
             
    }
     
}
