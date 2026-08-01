<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Settings;
use App\Category;
use App\Authors;
use App\Books; 

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image; 

class SettingsController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
          
    }
    public function general_settings()
    { 
    	if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.general');
        
        $settings = Settings::findOrFail('1');
 
        
        return view('admin.pages.settings.general_settings',compact('page_title','settings'));
    }	 
    
    public function update_general_settings(Request $request)
    {  
    	  
    	$settings = Settings::findOrFail('1');
 
	    
	    $data =  \Request::except(array('_token')) ;
	    
	    $rule=array(
		        'admin_logo' => 'required',
                'app_name' => 'required',
		        'app_logo' => 'required',
                'app_email' => 'required'
		   		 );
	    
	   	 $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
	    

	    $inputs = $request->all();

        putPermanentEnv('APP_TIMEZONE', $inputs['time_zone']);
  
        $settings->time_zone = $inputs['time_zone'];
        $settings->currency_code = $inputs['currency_code'];
         
        $settings->admin_logo = $inputs['admin_logo'];
            
		$settings->app_name = addslashes($inputs['app_name']); 
		$settings->app_logo = $inputs['app_logo'];
        $settings->app_email = $inputs['app_email'];  
        $settings->app_company = addslashes($inputs['app_company']);
        $settings->app_website = addslashes($inputs['app_website']);
        $settings->app_contact = addslashes($inputs['app_contact']);
        $settings->app_version = addslashes($inputs['app_version']);
        

        $settings->facebook_link = addslashes($inputs['facebook_link']);
        $settings->twitter_link = addslashes($inputs['twitter_link']);
        $settings->instagram_link = addslashes($inputs['instagram_link']);
        $settings->youtube_link = addslashes($inputs['youtube_link']);

        $settings->google_play_link = addslashes($inputs['google_play_link']);
        $settings->apple_store_link = addslashes($inputs['apple_store_link']);
                
		  
	    $settings->save(); 
        
 
	    Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }
    
    public function email_settings()
    { 
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.smtp_email');
        
        $settings = Settings::findOrFail('1');
 
        
        return view('admin.pages.settings.email_settings',compact('page_title','settings'));
    }

    public function update_email_settings(Request $request)
    {  
          
        $settings = Settings::findOrFail('1');
 
        
        $data =  \Request::except(array('_token')) ;
        
        $rule=array(
                'smtp_host' => 'required',
                'smtp_port' => 'required',
                'smtp_email' => 'required',
                'smtp_password' => 'required' 
                 );
        
         $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
        

        $inputs = $request->all();
        
        putPermanentEnv('MAIL_HOST', $inputs['smtp_host']);
        putPermanentEnv('MAIL_PORT', $inputs['smtp_port']);
        putPermanentEnv('MAIL_USERNAME', $inputs['smtp_email']);
        putPermanentEnv('MAIL_PASSWORD', $inputs['smtp_password']);
        putPermanentEnv('MAIL_ENCRYPTION', $inputs['smtp_encryption']);
        putPermanentEnv('MAIL_FROM_ADDRESS', $inputs['smtp_email']);
        
        $settings->smtp_host = $inputs['smtp_host'];
        $settings->smtp_port = $inputs['smtp_port'];
        $settings->smtp_email = $inputs['smtp_email'];
        $settings->smtp_password = $inputs['smtp_password'];
        $settings->smtp_encryption = $inputs['smtp_encryption'];

        $settings->save(); 
 
        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }
  
    public function onesignal_notification()
    { 
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.onesignal_notification');
        
        $settings = Settings::findOrFail('1');
 
        
        return view('admin.pages.settings.onesignal_notification',compact('page_title','settings'));
    }

    public function update_onesignal_notification(Request $request)
    {  
          
        $settings = Settings::findOrFail('1');
  
        $data =  \Request::except(array('_token')) ;  

        $rule=array(
                'onesignal_app_id' => 'required',
                'onesignal_rest_key' => 'required'                 
                 );
        
         $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }      
          
        $inputs = $request->all();
     
        
        $settings->onesignal_app_id = $inputs['onesignal_app_id'];
        $settings->onesignal_rest_key = $inputs['onesignal_rest_key']; 
       
        $settings->save(); 
 
        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function app_update_popup()
    { 
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.app_update_popup');
        
        $settings = Settings::findOrFail('1');
 
        
        return view('admin.pages.settings.app_update_popup',compact('page_title','settings'));
    }

    public function update_app_update_popup(Request $request)
    {  
          
        $settings = Settings::findOrFail('1');
  
        $data =  \Request::except(array('_token')) ;  
 
        $inputs = $request->all();
     
        
        $settings->app_update_hide_show = $inputs['app_update_hide_show'];  
        $settings->app_update_version_code = $inputs['app_update_version_code'];  
        $settings->app_update_desc = addslashes($inputs['app_update_desc']);
        $settings->app_update_link = $inputs['app_update_link'];    
        $settings->app_update_cancel_option = $inputs['app_update_cancel_option'];
       
        $settings->save(); 
 
        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function others_settings()
    { 
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.others_settings');
        
        $settings = Settings::findOrFail('1');
 
        
        return view('admin.pages.settings.others_settings',compact('page_title','settings'));
    }

    public function update_others_settings(Request $request)
    {  
          
        $settings = Settings::findOrFail('1');
  
        $data =  \Request::except(array('_token')) ;  
 
        $inputs = $request->all();

        $settings->cat_by_name_id = $inputs['cat_by_name_id'];
        $settings->cat_order_by = $inputs['cat_order_by'];
        $settings->subcat_by_name_id = $inputs['subcat_by_name_id'];
        $settings->subcat_order_by = $inputs['subcat_order_by'];
        $settings->author_by_name_id = $inputs['author_by_name_id'];
        $settings->author_order_by = $inputs['author_order_by'];
        $settings->book_by_name_id = $inputs['book_by_name_id'];
        $settings->book_order_by = $inputs['book_order_by'];
     
        $settings->pagination_limit = $inputs['pagination_limit'];
        $settings->latest_limit = $inputs['latest_limit'];
        $settings->continue_read_limit = $inputs['continue_read_limit'];
        $settings->trending_limit = $inputs['trending_limit'];  
        
        $settings->save(); 
 
        Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }


    public function notification_send()
    { 
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }

          $category_list = Category::orderBy('category_name','ASC')->get();
          $authors_list = Authors::orderBy('name','ASC')->get();
          $books_list = Books::orderBy('id','DESC')->get();
           

        $page_title=trans('words.android_app_notification_t');
         
        return view('admin.pages.notification_send',compact('page_title','category_list','authors_list','books_list'));
    }
    
    public function send_android_notification(Request $request)
    {  
          
        $settings = Settings::findOrFail('1');
 
        
        $data =  \Request::except(array('_token')) ;
        
        $rule=array(
                'notification_title' => 'required',
                'notification_msg' => 'required' 
                 );
        
         $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
        

        $inputs = $request->all();

        //Onesignal info 
        $onesignal_app_id=$settings->onesignal_app_id;
        $onesignal_rest_key=$settings->onesignal_rest_key;
        
        if($onesignal_app_id=='' OR $onesignal_rest_key=='')
        {
            Session::flash('flash_error_message', 'Onesignal app id or rest key not set.');

            return redirect()->back();
        } 

        $notification_title= $inputs['notification_title'];
        $notification_msg= $inputs['notification_msg'];
        $notification_image=$inputs['notification_image'];
        
        $section_type=$inputs['section_type'];
 
        if($inputs['category_id']!="")
        {
            $post_id=$inputs['category_id'];
            $post_title=stripslashes(Category::getCategoryInfo($post_id,'category_name'));
        }
        else if($inputs['author_id']!="")
        {
            $post_id=$inputs['author_id'];
            $post_title=stripslashes(Authors::getAuthorsInfo($post_id,'name'));
        }
        else if($inputs['book_id']!="")
        {
            $post_id=$inputs['book_id'];
            $post_title=stripslashes(Books::getBookInfo($post_id,'title'));
        }         
        else
        { 
            $post_id='';
            $post_title='';
        }    

        if($inputs['external_link']!="")
        {
        $external_link = $inputs['external_link'];
        }
        else
        {
        $external_link = false;
        }
        
        if($notification_image!='')
        {
                 

                $file_path = \URL::to('/'.$notification_image);
                 
                $content = array(
                         "en" => $notification_msg
                          );

                $fields = array(
                                'app_id' => $onesignal_app_id,
                                'included_segments' => array('All'),                                            
                                'data' => array("foo" => "bar", "type"=>$section_type,"post_id"=>$post_id,"post_title"=>$post_title,"external_link"=>$external_link),
                                'headings'=> array("en" => $notification_title),
                                'contents' => $content,
                                'big_picture' =>$file_path,
                                'ios_attachments' => array(
                                     'id' => $file_path,
                                ),                     
                                );

                $fields = json_encode($fields);
                //print("\nJSON sent:\n");
                //print($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                           'Authorization: Basic '.$onesignal_rest_key));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);
        } 
        else
        {
            $content = array(
                         "en" => $notification_msg
                          );

            $fields = array(
                            'app_id' => $onesignal_app_id,
                            'included_segments' => array('All'),                                      
                            'data' => array("foo" => "bar", "type"=>$section_type,"post_id"=>$post_id,"post_title"=>$post_title,"external_link"=>$external_link),
                            'headings'=> array("en" => $notification_title),
                            'contents' => $content
                            );

            $fields = json_encode($fields);
            //print("\nJSON sent:\n");
            //print($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                       'Authorization: Basic '.$onesignal_rest_key));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
        }        
 
        Session::flash('flash_message', trans('words.android_app_notification_msg'));

        return redirect()->back();
    } 
    

    public function verify_purchase_app()
    { 
        if(Auth::User()->usertype!="Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title = trans('words.app_verify');

        $settings = Settings::findOrFail('1');

        return view('admin.pages.verify_purchase_app',compact('page_title','settings'));
    } 

    public function verify_purchase_app_update(Request $request)
    {       

            $data =  \Request::except(array('_token'));
        
            $rule=array(                
                'buyer_name' => 'required',
                'purchase_code' => 'required',
                'app_package_name' => 'required'                              
                 );
        
            $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
        

            $inputs = $request->all();

            $buyer_name=trim($inputs['buyer_name']);
            $purchase_code=trim($inputs['purchase_code']);
            $app_package_name=trim($inputs['app_package_name']);

            $buyer_domain_url=\URL::to('/');
            $buyer_domain_ip=\Request::server('SERVER_ADDR');


            $envato_buyer= verify_envato_purchase_code(trim($purchase_code));

            if($envato_buyer->buyer==$buyer_name)
            {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"http://www.secureapp.viaviweb.in/verified_user.php");
                    curl_setopt($ch, CURLOPT_POST, true);                     
                    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array('envato_product_id' => $envato_buyer->item->id,'envato_buyer_name' => $buyer_name,'envato_purchase_code' => $purchase_code,'envato_purchased_status' => 1,'buyer_admin_url' => $buyer_domain_url,'package_name' => $app_package_name,'envato_buyer_email' => '')));

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec($ch);
                    curl_close ($ch);

                    $settings = Settings::findOrFail('1');
                    $settings->envato_buyer_name = $buyer_name;
                    $settings->envato_purchase_code = $purchase_code;
                    $settings->app_package_name = $app_package_name;       
                    $settings->save();  
                        
                    Session::flash('flash_message', 'Verify success');
                    return redirect()->back();
                      
            }
            else
            { 
                Session::flash('error_flash_message', 'Verify failed');
                return redirect()->back();
            }

            
    } 

    public function api_urls()
    { 
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.app_api');
         
        
        return view('admin.pages.api_urls',compact('page_title'));
    }    
    	
}
