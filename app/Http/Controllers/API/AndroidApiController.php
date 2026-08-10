<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User; 
use App\AppAds;
use App\Category;
use App\SubCategory;
use App\Authors;
use App\Books;
use App\MediaPost;
use App\MediaLike;
use App\MediaComment;
use App\MediaNotification;
use App\HomeSections; 
use App\Pages;
use App\Reports;
use App\Settings;
use App\PostViewsDownload;
use App\PostRatings;
use App\Favourite;
use App\PasswordReset;
use App\PaymentGateway;
use App\SubscriptionPlan;
use App\Transactions;
use App\ContinueRead;
use App\UserDownload;
use App\RentInfo;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;
use URL;
use Illuminate\Support\Facades\Password;

require(base_path() . '/public/stripe-php/init.php'); 

require(base_path() . '/public/paytm/PaytmChecksum.php');

require(base_path() . '/public/razorpay/vendor/autoload.php');
 
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;


class AndroidApiController extends MainAPIController
{
    public function __construct()
    {
        $this->pagination_limit=getcong('pagination_limit')?getcong('pagination_limit'):10;

        $this->cat_by_name_id=getcong('cat_by_name_id')?getcong('cat_by_name_id'):'category_name';
        $this->cat_order_by=getcong('cat_order_by')?getcong('cat_order_by'):'ASC';

        $this->subcat_by_name_id=getcong('subcat_by_name_id')?getcong('subcat_by_name_id'):'sub_category_name';
        $this->subcat_order_by=getcong('subcat_order_by')?getcong('subcat_order_by'):'ASC';

        $this->author_by_name_id=getcong('author_by_name_id')?getcong('author_by_name_id'):'name';
        $this->author_order_by=getcong('author_order_by')?getcong('author_order_by'):'ASC';

        $this->book_by_name_id=getcong('book_by_name_id')?getcong('book_by_name_id'):'title';
        $this->book_order_by=getcong('book_order_by')?getcong('book_order_by'):'ASC';
        
    }
      
    public function index()
    {   
          $response[] = array('msg' => "API",'success'=>'1'); 
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
        ));   
         
    }
    public function app_details()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        if(isset($get_data['user_id']) && $get_data['user_id']!='')
        {
            $user_id=$get_data['user_id'];        
            $user_info = User::getUserInfo($user_id);

           if(!empty($user_info))
           {
                if($user_info!='' AND $user_info->status==1)
                {
                    $user_status=true;
                }
                else
                {
                    $user_status=false;
                }
            }
            else
            {
               $user_status=false; 
            }
        }
        else
        {
            $user_status=false;
        }
 
        $settings = Settings::findOrFail('1'); 

        $default_language=$settings->default_language;
        $currency_code=$settings->currency_code;
        $app_name=$settings->app_name;
        $app_email=$settings->app_email;
        $app_logo=\URL::to('/'.$settings->app_logo);
        $app_company=$settings->app_company?$settings->app_company:'';
        $app_website=$settings->app_website?$settings->app_website:'';
        $app_contact=$settings->app_contact?$settings->app_contact:'';
        $app_version=$settings->app_version?$settings->app_version:'';

        $facebook_link=$settings->facebook_link?$settings->facebook_link:'';
        $twitter_link=$settings->twitter_link?$settings->twitter_link:'';
        $instagram_link=$settings->instagram_link?$settings->instagram_link:'';
        $youtube_link=$settings->youtube_link?$settings->youtube_link:'';
        $google_play_link=$settings->google_play_link?$settings->google_play_link:'';
        $apple_store_link=$settings->apple_store_link?$settings->apple_store_link:'';
         

        $app_update_hide_show=$settings->app_update_hide_show;
        $app_update_version_code=$settings->app_update_version_code?$settings->app_update_version_code:'1';
        $app_update_desc=stripslashes($settings->app_update_desc);
        $app_update_link=$settings->app_update_link;
        $app_update_cancel_option=$settings->app_update_cancel_option;

        $live_wallpaper_on_off=$settings->live_wallpaper_on_off;

          
        $app_package_name=$settings->app_package_name?$settings->app_package_name:"com.example.androidebookapps";

        //Ad List
        $ads_list = AppAds::where('status','1')->orderby('id')->get();  
        
        if(count($ads_list) > 0)
        {
            foreach($ads_list as $ad_data)
            {
                    $ad_id= $ad_data->id;
                    $ads_name= $ad_data->ads_name; 
                    $ads_info= json_decode($ad_data->ads_info);                  
                    $status= $ad_data->status?"true":"false";;                 
                     
                    $ads_obj[]=array("ad_id"=>$ad_id,"ads_name"=>$ads_name,"ads_info"=>$ads_info,"status"=>$status);   
            }
        }
        else
        {
            $ads_obj= array();
        }

        //Page List
        $page_list = Pages::where('status','1')->orderby('page_order')->get();  
  
        foreach($page_list as $page_data)
        {
                $page_id= $page_data->id;
                $page_title= stripslashes($page_data->page_title); 
                $page_content= stripslashes($page_data->page_content);                  
                  
                $pages_obj[]=array("page_id"=>$page_id,"page_title"=>$page_title,"page_content"=>$page_content);   
        }

        /***********Save visitor user info start************/
        $user_ip=\Request::ip();                
        $os_name = isset($get_data['os_name'])?$get_data['os_name']:"";   
        $browser_name = '';    
 
        save_visitor_analytics_info($user_ip,$os_name,$browser_name);

        /***********Save visitor user info end************/

        $response[] = array('app_package_name'=>$app_package_name,'default_language' => $default_language,'currency_code' => $currency_code,'app_name' => $app_name,'app_email' => $app_email,'app_logo' => $app_logo,'app_company' => $app_company,'app_website' => $app_website,'app_contact' => $app_contact,'facebook_link' => $facebook_link,'twitter_link' => $twitter_link,'instagram_link' => $instagram_link,'youtube_link' => $youtube_link,'google_play_link' => $google_play_link,'apple_store_link' => $apple_store_link,'app_version' => $app_version,'app_update_hide_show' => $app_update_hide_show,'app_update_version_code' => $app_update_version_code,'app_update_desc' => $app_update_desc,'app_update_link' => $app_update_link,'app_update_cancel_option' => $app_update_cancel_option,'live_wallpaper_on_off' => $live_wallpaper_on_off,'ads_list'=>$ads_obj,'page_list'=>$pages_obj,'success'=>'1');

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'user_status' => $user_status,
             'status_code' => 200,
             'success' => 1
        )); 

    }

    public function payment_settings()
    {
        $get_data=checkSignSalt($_POST['data']);

        $settings = Settings::findOrFail('1'); 
        
        $gateway_list = PaymentGateway::where('status','1')->orderby('id')->get(); 

        $settings = Settings::findOrFail('1'); 

        $currency_code=$settings->currency_code;
        
        if(count($gateway_list))
        {
            foreach($gateway_list as $gateway_data)
            {
                    $gateway_id= $gateway_data->id;
                    $gateway_name= $gateway_data->gateway_name; 
                    $gateway_logo= \URL::to('/admin_assets/images/gateway/'.$gateway_data->id.'.png');
                    $gateway_info= json_decode($gateway_data->gateway_info);                  
                    $status= $gateway_data->status?"true":"false";              
                    
                    $response[]=array("gateway_id"=>$gateway_id,"gateway_name"=>$gateway_name,"gateway_logo"=>$gateway_logo,"gateway_info"=>$gateway_info,"status"=>$status);   
            }    
        }
        else
        {
            $response=array();    
        }
        

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'currency_code' => $currency_code,
            'status_code' => 200,
            'success' => 1
        ));
 
    }
    
    public function home()
    {   

        $get_data=checkSignSalt($_POST['data']);

       $user_id= isset($get_data['user_id'])?$get_data['user_id']:"";
         
        $featured_books= Books::where('status',1)->where('featured',1)->orderby($this->book_by_name_id,$this->book_order_by)->get();

        if(count($featured_books) >0)
        {
            foreach($featured_books as $featured_data)
            { 
                $author_ids= $featured_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids,1) as $author_id)
                    {
                        $author_name= Authors::getAuthorsInfo($author_id,'name');

                        //$author_list=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    //$author_list=array();

                    $author_name='';
                }

                $response['featured_books'][]=array("post_id"=>$featured_data->id,"post_access"=>$featured_data->book_access,"post_title"=>stripslashes($featured_data->title),"post_image"=>book_asset_url($featured_data->image),"author_name"=>$author_name);
            }
        }
        else
        {
            $response['featured_books']=array();
        }
 
        //Continue Reading
        $read_books= ContinueRead::where('user_id',$user_id)->orderby('id','DESC')->take(getcong('continue_read_limit'))->get();

        if(count($read_books) > 0)
        {
            foreach($read_books as $read_data)
            {   
                $post_id= $read_data->post_id;
                $page_num= stripslashes($read_data->page_num);

                $book_info = Books::where('id',$post_id)->where('status',1)->first();

                if($book_info)
                {
                    $post_access= $book_info->book_access;
                    $post_title= $book_info->title;
                    $post_image=book_asset_url($book_info->image);
                     

                    $response['continue_reading'][]=array("post_id"=>$post_id,"post_access"=>$post_access,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"page_num"=>$page_num);
                }
                
            }

            if(!isset($response['continue_reading']))
            {
                $response['continue_reading']=array();
            }

        }
        else
        {
            $response['continue_reading']=array();
        }
        

        //Trending Books         

        $trending_start_date = date('Y-m-d', strtotime('today - 7 days'));
        $trending_end_date = date('Y-m-d');

        $trending_now = PostViewsDownload::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('SUM(post_views) as total_views')->groupBy('post_id','post_type')->orderby('total_views','DESC')->take(getcong('trending_limit'))->get();

        if(count($trending_now)>0) 
        {
            foreach($trending_now as $book_data)
            {   
                $post_id= $book_data->post_id;
                $post_type="book";
                $post_access= Books::getBookInfo($post_id,'book_access');
                $post_title= Books::getBookInfo($post_id,'title');
                $post_image=book_asset_url(Books::getBookInfo($post_id,'image'));

                $book_on_rent=Books::getBookInfo($post_id,'book_on_rent');
                $book_rent_price=Books::getBookInfo($post_id,'book_rent_price')?Books::getBookInfo($post_id,'book_rent_price'):'';
                $book_rent_time=Books::getBookInfo($post_id,'book_rent_time')?Books::getBookInfo($post_id,'book_rent_time'):'';

                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $author_ids= Books::getBookInfo($post_id,'author_ids');

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                        $author_name= Authors::getAuthorsInfo($author_id,'name');

                        $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }
                     
                $response['trending_books'][]= array("post_id"=>$post_id,"post_access"=>$post_access,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response['trending_books']=array();
        }
        
         
        $home_sections = HomeSections::where('status',1)->orderby('id')->get();
        

        foreach($home_sections as $sections_data)
        {   
            if($sections_data->post_type=="category")
            {
                foreach(explode(",",$sections_data->post_ids) as $cat_data)
                {
                    $post_id=$cat_data;
                    $post_type="category";
                    $post_title= Category::getCategoryInfo($cat_data,'category_name');
                    $post_image=\URL::to('/'.Category::getCategoryInfo($cat_data,'category_image'));

                    $total_category= SubCategory::where('cat_id',$post_id)->count();

                    if($total_category >0)
                    {
                        $sub_cat_status =true;
                    }
                    else
                    {
                        $sub_cat_status =false;
                    }


                $home_content1[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"sub_cat_status"=>$sub_cat_status);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content1);

                unset($home_content1);
            }

            if($sections_data->post_type=="subcategory")
            {
                foreach(explode(",",$sections_data->post_ids) as $cat_data)
                {
                    $post_id=$cat_data;
                    $post_type="subcategory";
                    $post_title= SubCategory::getSubCategoryInfo($cat_data,'sub_category_name');
                    $post_image=\URL::to('/'.SubCategory::getSubCategoryInfo($cat_data,'sub_category_image'));

                $home_content1[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content1);

                unset($home_content1);
            }

            if($sections_data->post_type=="author")
            {
                foreach(explode(",",$sections_data->post_ids) as $author_data)
                {
                    $post_id=$author_data;
                    $post_type="author";
                    $post_title= Authors::getAuthorsInfo($author_data,'name');
                    $post_image=\URL::to('/'.Authors::getAuthorsInfo($author_data,'image'));
                     
                $home_content3[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content3);

                unset($home_content3);
            }

            if($sections_data->post_type=="book")
            {
                foreach(explode(",",$sections_data->post_ids) as $author_data)
                {
                    $post_id=$author_data;
                    $post_type="book";
                    $post_access= Books::getBookInfo($post_id,'book_access');
                    $post_title= Books::getBookInfo($author_data,'title');
                    $post_image=book_asset_url(Books::getBookInfo($author_data,'image'));

                    $book_on_rent=Books::getBookInfo($author_data,'book_on_rent');
                    $book_rent_price=Books::getBookInfo($author_data,'book_rent_price')?Books::getBookInfo($author_data,'book_rent_price'):'';
                    $book_rent_time=Books::getBookInfo($author_data,'book_rent_time')?Books::getBookInfo($author_data,'book_rent_time'):'';

                    $total_views= post_views_count($post_id,"Book");
                    $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                    $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                    $author_ids= Books::getBookInfo($author_data,'author_ids');

                    if(!empty($author_ids))
                    {
                        foreach(explode(',',$author_ids) as $author_id)
                        {
                            $author_name= Authors::getAuthorsInfo($author_id,'name');

                            $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                        }
                    }
                    else
                    {
                        $author_list=array();
                    }
                     
                    $home_content4[]= array("post_id"=>$post_id,"post_access"=>$post_access,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);       

                    unset($author_list);
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content4);

                unset($home_content4);
            }
 
        }
        
        // Latest Books (most recent) — same shape as trending, for the home row.
        $latest_books = Books::where('status',1)->orderby('id','DESC')->take(10)->get();
        if(count($latest_books) > 0)
        {
            foreach($latest_books as $lb)
            {
                $lb_author_list = array();
                if(!empty($lb->author_ids))
                {
                    foreach(explode(',',$lb->author_ids) as $aid)
                    {
                        $lb_author_list[] = array('author_id'=>$aid, 'author_name'=>Authors::getAuthorsInfo($aid,'name'));
                    }
                }
                $response['latest_books'][] = array(
                    "post_id"=>$lb->id,
                    "post_access"=>$lb->book_access,
                    "post_type"=>"Book",
                    "post_title"=>stripslashes($lb->title),
                    "post_image"=>$lb->coverUrl(),
                    "cover_color"=>(\App\Category::getCategoryInfo($lb->cat_id,'category_color') ?: '#4a7dff'),
                    "content_type"=>($lb->content_type ?: "book"),
                    "video_id"=>($lb->url_type=="youtube" ? youtube_id($lb->url) : ""),
                    "book_on_rent"=>($lb->book_on_rent ?: '0'),
                    "book_rent_price"=>($lb->rent_price ?: '0'),
                    "book_rent_time"=>($lb->rent_time ?: '0'),
                    "total_views"=>post_views_count($lb->id,"Book"),
                    "total_rate"=>PostRatings::getPostTotalRatings($lb->id,"Book"),
                    "favourite"=>check_favourite("Book",$lb->id,$user_id),
                    "author_list"=>$lb_author_list
                );
                unset($lb_author_list);
            }
        }
        else
        {
            $response['latest_books']=array();
        }

        // Latest Feed posts (approved) — horizontal thumbnails on home.
        $feed_posts = MediaPost::where('upload_status','approved')->where('status',1)
                        ->orderby('id','DESC')->take(10)->get();
        if(count($feed_posts) > 0)
        {
            foreach($feed_posts as $fp)
            {
                $response['feed_posts'][] = array(
                    "post_id"    => $fp->id,
                    "media_type" => $fp->media_type,
                    "title"      => stripslashes($fp->title),
                    "thumb_url"  => $fp->thumb_url ?: '',
                    "file_url"   => $fp->file_url ?: ''
                );
            }
        }
        else
        {
            $response['feed_posts']=array();
        }
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

    public function home_section()
    {
        $get_data=checkSignSalt($_POST['data']);

        $id = $get_data['id'];
         
        $sections_data = HomeSections::where('id',$id)->where('status',1)->first();

            if($sections_data->post_type=="category")
            {
                foreach(explode(",",$sections_data->post_ids) as $cat_data)
                {
                    $post_id=$cat_data;
                    $post_type="category";
                    $post_title= Category::getCategoryInfo($cat_data,'category_name');
                    $post_image=\URL::to('/'.Category::getCategoryInfo($cat_data,'category_image'));

                    $total_category= SubCategory::where('cat_id',$post_id)->count();

                    if($total_category >0)
                    {
                        $sub_cat_status =true;
                    }
                    else
                    {
                        $sub_cat_status =false;
                    }


                $home_content1[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"sub_cat_status"=>$sub_cat_status);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content1);

                unset($home_content1);
            }

            if($sections_data->post_type=="subcategory")
            {
                foreach(explode(",",$sections_data->post_ids) as $cat_data)
                {
                    $post_id=$cat_data;
                    $post_type="subcategory";
                    $post_title= SubCategory::getSubCategoryInfo($cat_data,'sub_category_name');
                    $post_image=\URL::to('/'.SubCategory::getSubCategoryInfo($cat_data,'sub_category_image'));

                $home_content1[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content1);

                unset($home_content1);
            }

            if($sections_data->post_type=="author")
            {
                foreach(explode(",",$sections_data->post_ids) as $author_data)
                {
                    $post_id=$author_data;
                    $post_type="author";
                    $post_title= Authors::getAuthorsInfo($author_data,'name');
                    $post_image=\URL::to('/'.Authors::getAuthorsInfo($author_data,'image'));
                     
                $home_content3[]= array("post_id"=>$post_id,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image);       
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content3);

                unset($home_content3);
            }

            if($sections_data->post_type=="book")
            {
                foreach(explode(",",$sections_data->post_ids) as $author_data)
                {
                    $post_id=$author_data;
                    $post_type="book";
                    $post_access= Books::getBookInfo($post_id,'book_access');
                    $post_title= Books::getBookInfo($author_data,'title');
                    $post_image=book_asset_url(Books::getBookInfo($author_data,'image'));

                    $book_on_rent=Books::getBookInfo($author_data,'book_on_rent');
                    $book_rent_price=Books::getBookInfo($author_data,'book_rent_price')?Books::getBookInfo($author_data,'book_rent_price'):'';
                    $book_rent_time=Books::getBookInfo($author_data,'book_rent_time')?Books::getBookInfo($author_data,'book_rent_time'):'';

                    $total_views= post_views_count($post_id,"Book");
                    $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                    $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                    $author_ids= Books::getBookInfo($author_data,'author_ids');

                    if(!empty($author_ids))
                    {
                        foreach(explode(',',$author_ids) as $author_id)
                        {
                            $author_name= Authors::getAuthorsInfo($author_id,'name');

                            $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                        }
                    }
                    else
                    {
                        $author_list=array();
                    }
                     
                    $home_content4[]= array("post_id"=>$post_id,"post_access"=>$post_access,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);       

                    unset($author_list);
 
                }

                $response['home_sections'][]=array("home_id"=>$sections_data->id,"home_title"=>$sections_data->section_name,"home_type"=>$sections_data->post_type,"home_content"=>$home_content4);

                unset($home_content4);
            }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

    public function continue_read_list()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $user_id= isset($get_data['user_id'])?$get_data['user_id']:"";

        $read_books= ContinueRead::where('user_id',$user_id)->orderby('id','DESC')->paginate($this->pagination_limit);

        $total_records=ContinueRead::where('user_id',$user_id)->count();

        if(count($read_books) > 0 AND $total_records > 0)
        {       
            
            foreach($read_books as $read_data)
            {   
                $post_id= $read_data->post_id;

                $page_num= stripslashes($read_data->page_num);


                $book_info = Books::where('id',$post_id)->where('status',1)->first();

                if($book_info)
                {
                    $post_access= $book_info->book_access;
                    $post_title= $book_info->title;
                    $post_image=book_asset_url($book_info->image);

                    $response[]=array("post_id"=>$post_id,"post_access"=>$post_access,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"page_num"=>$page_num);
                }
                
            }

            if(!isset($response))
            {
                $response=array();
            }

        }
        else
        {
            $response=array();
        }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'total_records' => $total_records,
            'success' => 1
        ));

    }
    
    public function trending_books()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $trending_start_date = date('Y-m-d', strtotime('today - 7 days'));
        $trending_end_date = date('Y-m-d');

        $trending_now = PostViewsDownload::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('SUM(post_views) as total_views')->groupBy('post_id','post_type')->orderby('total_views','DESC')->take(20)->get();

        if(count($trending_now)>0) 
        {
            foreach($trending_now as $book_data)
            {   
                $post_id= $book_data->post_id;
                $post_type="book";
                $post_access= Books::getBookInfo($post_id,'book_access');
                $post_title= Books::getBookInfo($post_id,'title');
                $post_image=book_asset_url(Books::getBookInfo($post_id,'image'));

                $book_on_rent=Books::getBookInfo($post_id,'book_on_rent');
                $book_rent_price=Books::getBookInfo($post_id,'book_rent_price')?Books::getBookInfo($post_id,'book_rent_price'):'';
                $book_rent_time=Books::getBookInfo($post_id,'book_rent_time')?Books::getBookInfo($post_id,'book_rent_time'):'';

                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $author_ids= Books::getBookInfo($post_id,'author_ids');

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                        $author_name= Authors::getAuthorsInfo($author_id,'name');

                        $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }
                     
                $response[]= array("post_id"=>$post_id,"post_access"=>$post_access,"post_type"=>$post_type,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response=array();
        }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

    public function latest_books()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $latest_user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $latest_query = Books::where('status',1);
        $latest_query = apply_department_priority($latest_query, $latest_user_id, 'id', 'DESC');
        $data_list= $latest_query->take(getcong('latest_limit'))->get();
 
        if(count($data_list) > 0)
        {
            foreach($data_list as $obj_data)
            {   

                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';

  
                $response[]=array("post_id"=>$obj_data->id,"post_access"=>$obj_data->book_access,"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"book_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"content_type"=>($obj_data->content_type ?: "book"),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ""),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response=array();
        }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }
 
    public function category()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $data_list= Category::where('status',1)->orderby($this->cat_by_name_id,$this->cat_order_by)->paginate($this->pagination_limit);
        
        $total_records=Category::where('status',1)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {   
            foreach($data_list as $obj_data)
            { 
                $total_category= SubCategory::where('cat_id',$obj_data->id)->count();

                if($total_category >0)
                {
                    $sub_cat_status =true;
                }
                else
                {
                    $sub_cat_status =false;
                }

                $response[]=array("post_id"=>$obj_data->id,"post_title"=>stripslashes($obj_data->category_name),"post_image"=>\URL::to('/'.$obj_data->category_image),"sub_cat_status"=>$sub_cat_status);
            }
        }
        else
        {
            $response = array();
        }


         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function subcategory()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $cat_id=$get_data['cat_id'];

        $data_list= SubCategory::where('status',1)->where('cat_id',$cat_id)->orderby($this->subcat_by_name_id,$this->subcat_order_by)->paginate($this->pagination_limit);

        $total_records=SubCategory::where('status',1)->where('cat_id',$cat_id)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            { 
                $total_books= Books::where('sub_cat_id',$obj_data->id)->count();

                $response[]=array("post_id"=>$obj_data->id,"post_title"=>stripslashes($obj_data->sub_category_name),"post_image"=>\URL::to('/'.$obj_data->sub_category_image),"total_books"=>$total_books);
            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function authors()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $data_list= Authors::where('status',1)->orderby($this->author_by_name_id,$this->author_order_by)->paginate($this->pagination_limit);
        
        $total_records=Authors::where('status',1)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            {  
                $response[]=array("post_id"=>$obj_data->id,"post_title"=>stripslashes($obj_data->name),"post_image"=>book_asset_url($obj_data->image));
            }
        }
        else
        {
            $response = array();
        }


         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function books_by_cat()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $cat_id=$get_data['cat_id'];

        $cat_user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $cat_query = Books::where('status',1)->where('cat_id',$cat_id);
        $cat_query = apply_department_priority($cat_query, $cat_user_id, $this->book_by_name_id, $this->book_order_by);
        $data_list= $cat_query->paginate($this->pagination_limit);

        $total_records=Books::where('status',1)->where('cat_id',$cat_id)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            {   

                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';

  
                $response[]=array("post_id"=>$obj_data->id,"post_access"=>$obj_data->book_access,"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"book_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"content_type"=>($obj_data->content_type ?: "book"),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ""),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function books_by_sub_cat()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $sub_cat_id=$get_data['sub_cat_id'];

        $subcat_user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $subcat_query = Books::where('status',1)->where('sub_cat_id',$sub_cat_id);
        $subcat_query = apply_department_priority($subcat_query, $subcat_user_id, $this->book_by_name_id, $this->book_order_by);
        $data_list= $subcat_query->paginate($this->pagination_limit);

        $total_records=Books::where('status',1)->where('sub_cat_id',$sub_cat_id)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            { 
                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';
 
                $response[]=array("post_id"=>$obj_data->id,"post_access"=>$obj_data->book_access,"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"book_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"content_type"=>($obj_data->content_type ?: "book"),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ""),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);

            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function books_by_author()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $author_id=$get_data['author_id'];

        $author_user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $author_query = Books::where('status',1)->whereRaw("find_in_set('$author_id',author_ids)");
        $author_query = apply_department_priority($author_query, $author_user_id, $this->book_by_name_id, $this->book_order_by);
        $data_list= $author_query->paginate($this->pagination_limit);

        $total_records=Books::where('status',1)->whereRaw("find_in_set('$author_id',author_ids)")->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            { 
                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';
 
                $response[]=array("post_id"=>$obj_data->id,"post_access"=>$obj_data->book_access,"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"book_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"content_type"=>($obj_data->content_type ?: "book"),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ""),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function author_info()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $author_id=$get_data['author_id'];

        $author_info= Authors::where('id',$author_id)->first();
  
        $response[]=array("author_id"=>$author_info->id,"author_name"=>stripslashes($author_info->name),"author_info"=>stripslashes($author_info->info),"author_image"=>book_asset_url($author_info->image), "facebook_url"=>$author_info->facebook_url,"instagram_url"=>$author_info->instagram_url,"youtube_url"=>$author_info->youtube_url,"website_url"=>$author_info->website_url);
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function books_details()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $user_id=isset($get_data['user_id'])?$get_data['user_id']:'';

        if($user_id!="")
        {
            $user_plan_status= check_app_user_plan($user_id);
        }
        else
        {
            $user_plan_status=false;
        }

        $book_id=$get_data['book_id'];

        $obj_data= Books::where('status',1)->where('id',$book_id)->first();
 
        if($obj_data)
        {
             
                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $total_reviews= post_total_reviews_count($post_id,"Book"); 
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");
 

                //Related Books
                $cat_id=$obj_data->cat_id;

                $related_data= Books::where('cat_id',$cat_id)->where('id','!=',$post_id)->take(5)->get();

                if(count($related_data)>0)
                {
                    foreach($related_data as $rel_book_data)
                    { 
                        $author_ids= $rel_book_data->author_ids;

                        if(!empty($author_ids))
                        {
                            foreach(explode(',',$author_ids) as $author_id)
                            {
                                 $author_name= Authors::getAuthorsInfo($author_id,'name');
        
                                 $rel_author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                            }
                        }
                        else
                        {
                            $rel_author_list=array();
                        }
        
                        $post_id = $rel_book_data->id;
                        
                        $r_total_views= post_views_count($post_id,"Book");
                        $r_total_rate= PostRatings::getPostTotalRatings($post_id,"Book");
                         
                        $r_favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                        $book_on_rent=$rel_book_data->book_on_rent;
                        $book_rent_price=$rel_book_data->book_rent_price?$rel_book_data->book_rent_price:'';
                        $book_rent_time=$rel_book_data->book_rent_time?$rel_book_data->book_rent_time:'';
         
                        $related_books[]=array("post_id"=>$rel_book_data->id,"post_access"=>$rel_book_data->book_access,"cat_id"=>$rel_book_data->cat_id,"sub_cat_id"=>$rel_book_data->sub_cat_id,"post_title"=>stripslashes($rel_book_data->title),"post_description"=>stripslashes($rel_book_data->description),"post_image"=>book_asset_url($rel_book_data->image),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$r_total_views,"total_rate"=>$r_total_rate,"favourite"=>$r_favourite,"author_list"=>$rel_author_list);

                        unset($rel_author_list);
                    }
                }
                else
                {
                    $related_books=array();
                }

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';
                
                if($obj_data->url_type=="server_url")
                {
                    $post_file_url= $obj_data->url;
                }
                elseif($obj_data->url_type=="youtube")
                {
                    $post_file_url= youtube_watch($obj_data->url);
                }
                else
                {
                    $post_file_url= \URL::to('/'.$obj_data->url);
                }
                
                $book_purchased = check_on_rent($user_id,$obj_data->id,'Book');

                $share_url= \URL::to('share/book/'.$obj_data->id);

                $continue_page_num = get_book_continue_page_num($obj_data->id,$user_id);

                $response[]=array("continue_page_num"=>stripslashes($continue_page_num),"user_plan_status"=>$user_plan_status,"post_id"=>$obj_data->id,"content_type"=>($obj_data->content_type ?: 'book'),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ''),"video_url"=>($obj_data->url_type=="youtube" ? youtube_watch($obj_data->url) : ''),"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"post_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"post_url_type"=>$obj_data->url_type,"post_file_url"=>$post_file_url,"download_enable"=>$obj_data->download_enable,"rewarded_ad"=>$obj_data->rewarded_ad,"cover_color"=>(\App\Category::getCategoryInfo($obj_data->cat_id,'category_color') ?: '#4a7dff'),"book_purchased"=>$book_purchased,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"share_url"=>$share_url,"total_views"=>$total_views,"total_rate"=>$total_rate,"total_reviews"=>$total_reviews,"favourite"=>$favourite,"author_list"=>$author_list,"related_books"=>$related_books);
            
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function search_book()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $search_text=$get_data['search_text'];

        //$type=isset($get_data['wall_type'])?$get_data['wall_type']:'';
        //$color_id=isset($get_data['color_id'])?$get_data['color_id']:''; 
         
        $data_list= Books::where('status',1)->where('title','LIKE','%'.$search_text.'%');
        $search_user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $data_list= apply_department_priority($data_list, $search_user_id, $this->book_by_name_id, $this->book_order_by)->paginate($this->pagination_limit);
        
        $total_records=Books::where('status',1)->where('title','LIKE','%'.$search_text.'%')->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {
            foreach($data_list as $obj_data)
            { 
                $author_ids= $obj_data->author_ids;

                if(!empty($author_ids))
                {
                    foreach(explode(',',$author_ids) as $author_id)
                    {
                         $author_name= Authors::getAuthorsInfo($author_id,'name');

                         $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                    }
                }
                else
                {
                    $author_list=array();
                }

                $post_id = $obj_data->id;
                
                $total_views= post_views_count($post_id,"Book");
                $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                $book_on_rent=$obj_data->book_on_rent;
                $book_rent_price=$obj_data->book_rent_price?$obj_data->book_rent_price:'';
                $book_rent_time=$obj_data->book_rent_time?$obj_data->book_rent_time:'';
 
                $response[]=array("post_id"=>$obj_data->id,"post_access"=>$obj_data->book_access,"cat_id"=>$obj_data->cat_id,"sub_cat_id"=>$obj_data->sub_cat_id,"book_access"=>$obj_data->book_access,"post_title"=>stripslashes($obj_data->title),"content_type"=>($obj_data->content_type ?: "book"),"video_id"=>($obj_data->url_type=="youtube" ? youtube_id($obj_data->url) : ""),"post_description"=>stripslashes($obj_data->description),"post_image"=>$obj_data->coverUrl(),"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                unset($author_list);
            }
        }
        else
        {
            $response=array();
        }
          

         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function filter_book()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $filter_type=$get_data['filter_type'];
        $filter_val=$get_data['filter_val'];

        if($filter_type=="sort_by")
        {
            if($filter_val=="Popularity")
            {
                //echo "test";exit;
                //Trending
                $trending_start_date = date('Y-m-d', strtotime('today - 30 days'));
                $trending_end_date = date('Y-m-d');

                $book_list = PostViewsDownload::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('SUM(post_views) as total_views')->groupBy('post_id','post_type')->orderby('total_views','DESC')->paginate($this->pagination_limit);

                $total_records= PostViewsDownload::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('SUM(post_views) as total_views')->groupBy('post_id','post_type')->count();
                 
            }
            else if($filter_val=="Ratings")
            {
                $trending_start_date = date('Y-m-d', strtotime('today - 365 days'));
                $trending_end_date = date('Y-m-d');

                $book_list = PostRatings::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('AVG(rate) as total_rate')->groupBy('post_id','post_type')->orderby('total_rate','DESC')->paginate($this->pagination_limit);

                $total_records=PostRatings::select("post_id","post_type")->whereBetween('date', array(strtotime($trending_start_date), strtotime($trending_end_date)))->selectRaw('AVG(rate) as total_rate')->groupBy('post_id','post_type')->count();
            }
            else
            {   
                //echo "test";exit;
                //NewArrival
                $book_list= Books::where('status',1)->orderby($this->book_by_name_id,$this->book_order_by)->paginate($this->pagination_limit);

                $total_records=Books::where('status',1)->count();
            }
        }
 
        if($filter_type=="author_by")
        {   
            $author_id = explode(',',$filter_val); 
            //exit;
           $book_list= Books::where('status',1)->where(function($query) use($author_id) {
                foreach($author_id as $term) {
                    //$query->orWhere('author_ids', $term);
                    $query->orwhereRaw("find_in_set('$term',author_ids)");
                };
            })->orderby($this->book_by_name_id,$this->book_order_by)->paginate($this->pagination_limit);
            
            $total_records=Books::where('status',1)->where(function($query) use($author_id) {
                foreach($author_id as $term) {
                    //$query->orWhere('author_ids', $term);
                    $query->orwhereRaw("find_in_set('$term',author_ids)");
                };
            })->orderby($this->book_by_name_id,$this->book_order_by)->count();
        }

        if($filter_type=="category_by")
        {   
            //$category_id = explode(',',$filter_val);  
            $category_id = json_decode("[$filter_val]", true);               

            $book_list= Books::where('status',1)->whereIn('cat_id',$category_id)->orderby($this->book_by_name_id,$this->book_order_by)->paginate($this->pagination_limit);

            $total_records=Books::where('status',1)->whereIn('cat_id',$category_id)->count();
        }


            if(isset($book_list) AND count($book_list) > 0)
            {
                
                    foreach($book_list as $book_data)
                    { 
                        $post_id = $book_data->post_id?$book_data->post_id:$book_data->id;
                         
                        $book_access= Books::getBookInfo($post_id,'book_access');
                        $cat_id= Books::getBookInfo($post_id,'cat_id');
                        $sub_cat_id= Books::getBookInfo($post_id,'sub_cat_id'); 
                                        
                        $post_title= Books::getBookInfo($post_id,'title');
                        $description= Books::getBookInfo($post_id,'description');
                        $post_image= book_asset_url(Books::getBookInfo($post_id,'image'));
                        
                        $author_ids= Books::getBookInfo($post_id,'author_ids');

                        if(!empty($author_ids))
                        {
                            foreach(explode(',',$author_ids) as $author_id)
                            {
                                $author_name= Authors::getAuthorsInfo($author_id,'name');

                                $author_list[]=array('author_id'=>$author_id,'author_name'=>$author_name);
                            }
                        }
                        else
                        {
                            $author_list=array();
                        }

                        $book_on_rent= Books::getBookInfo($post_id,'book_on_rent');

                        $rent_price = Books::getBookInfo($post_id,'book_rent_price');
                        $rent_time = Books::getBookInfo($post_id,'book_rent_time');

                        $book_rent_price=$rent_price?$rent_price:'';
                        $book_rent_time=$rent_time?$rent_time:'';
    
                        $total_views= post_views_count($post_id,"Book");
                        $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                        $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");

                        $response[]=array("post_id"=>$post_id,"post_access"=>$book_access,"cat_id"=>$cat_id,"sub_cat_id"=>$sub_cat_id,"book_access"=>$book_access,"post_title"=>stripslashes($post_title),"post_description"=>stripslashes($description),"post_image"=>$post_image,"book_on_rent"=>$book_on_rent,"book_rent_price"=>$book_rent_price,"book_rent_time"=>$book_rent_time,"total_views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"author_list"=>$author_list);

                        unset($author_list);
                    }
            }
            else
            {
                $response=array(); 
            }    
         

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));    
    }

    public function all_category()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $data_list= Category::where('status',1)->orderby($this->cat_by_name_id,$this->cat_order_by)->get();
        
        if(count($data_list) > 0)
        {
            foreach($data_list as $obj_data)
            { 
                $total_category= SubCategory::where('cat_id',$obj_data->id)->count();

                if($total_category >0)
                {
                    $sub_cat_status =true;
                }
                else
                {
                    $sub_cat_status =false;
                }

                $response[]=array("post_id"=>$obj_data->id,"post_title"=>stripslashes($obj_data->category_name),"post_image"=>\URL::to('/'.$obj_data->category_image),"sub_cat_status"=>$sub_cat_status);
            }
        }
        else
        {
            $response = array();
        }


         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function all_authors()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $data_list= Authors::where('status',1)->orderby($this->author_by_name_id,$this->author_order_by)->get();
        
       
        if(count($data_list) > 0)
        {
            foreach($data_list as $obj_data)
            {  
                $response[]=array("post_id"=>$obj_data->id,"post_title"=>stripslashes($obj_data->name),"post_image"=>book_asset_url($obj_data->image));
            }
        }
        else
        {
            $response = array();
        }


         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function books_reviews_list()
    {    

        $get_data=checkSignSalt($_POST['data']);

        $book_id=$get_data['book_id'];

        $reviews = PostRatings::where('post_id',$book_id)->orderBy('id','DESC')->get();

        if(count($reviews) > 0)
        {
            foreach($reviews as $reviews_data)
            {
                $user_name = User::getUserInfo($reviews_data->user_id,'name');
                $user_image = User::getUserInfo($reviews_data->user_id,'user_image'); 
                
                if($user_image)
                {
                    $user_image_url=user_image_url($user_image);
                }
                else
                {
                    $user_image_url='';
                }

                $response[]=array("review_id"=>$reviews_data->id,"user_id"=>$reviews_data->user_id,"rate"=>$reviews_data->rate,"review_text"=>stripslashes($reviews_data->review_text),"date"=>date('d, M Y',$reviews_data->date),"user_name"=>$user_name,"user_image"=>$user_image_url);
            }
        }
        else
        {
            $response=array();
        }
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));    

    }
     
 
    public function post_view()
    {           
        $get_data=checkSignSalt($_POST['data']);

        $post_id = $get_data['post_id'];
        $post_type = $get_data['post_type'];
          
        //View Update
        post_views_save($post_id,$post_type);

        $post_views= post_views_count($post_id,$post_type);

        $response[]=array("views"=>$post_views);
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function post_download()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id = $get_data['user_id'];
        $post_id = $get_data['post_id'];
        $post_type = $get_data['post_type'];
          
        //Download Update
        post_download_save($post_id,$post_type);

        $post_download= post_download_count($post_id,$post_type);

        //User Donwload
  
 
        $down_info = UserDownload::where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();   


        if(empty($down_info))
        { 
            $down_obj = new UserDownload;

            $down_obj->post_id = $post_id;
            $down_obj->user_id = $user_id;
            $down_obj->save();
        }
         
         
        $response[]=array("download"=>$post_download);
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function post_rate()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id = $get_data['user_id'];
        $post_id = $get_data['post_id'];
        $rate = $get_data['rate'];
        $post_type = $get_data['post_type'];

        $review_text = $get_data['review_text'];

        $rate_info = PostRatings::where('post_type', '=', $post_type)->where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();   


        if($rate_info)
        { 
            $rate_obj = PostRatings::findOrFail($rate_info->id);        
            $rate_obj->rate = $rate;
            $rate_obj->review_text = $review_text;
            $rate_obj->save();

            $msg=trans('words.rate_updated');
             
        }
        else
        {
            $rate_obj = new PostRatings;

            $rate_obj->post_id = $post_id;
            $rate_obj->user_id = $user_id;
            $rate_obj->rate = $rate;
            $rate_obj->post_type = $post_type;
            $rate_obj->review_text = $review_text;
            $rate_obj->date = strtotime(date('m/d/Y H:i:s'));
            $rate_obj->save();

            $msg=trans('words.rate_success');
        }  
         

        $total_rate= PostRatings::getPostTotalRatings($post_id,$post_type);

        $response[]=array("total_rate"=>$total_rate,"msg"=>$msg);
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function delete_user_review()
    { 
         
        $get_data=checkSignSalt($_POST['data']);

        $review_id = $get_data['review_id'];
         
        $rate_obj = PostRatings::where('id',$review_id)->first();        
       
        if($rate_obj)
        {
            $rate_obj->delete();
            $response[]=array("msg"=>trans('words.deleted'));
        }
        else
        {
            $response[]=array("msg"=>'Something went wrong');
        }
            
        
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
        
    }

    public function post_favourite()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id = $get_data['user_id'];
        $post_id = $get_data['post_id'];
        $post_type = $get_data['post_type'];
 
        $fav_info = Favourite::where('post_type', '=', $post_type)->where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();   


        if($fav_info)
        { 
            $fav_obj = Favourite::findOrFail($fav_info->id);        
            $fav_obj->delete();

            $success=false;
            $msg=trans('words.fav_deleted');
             
        }
        else
        {
            $fav_obj = new Favourite;

            $fav_obj->post_id = $post_id;
            $fav_obj->user_id = $user_id;
            $fav_obj->post_type = $post_type;
            $fav_obj->save();

            $success=true;
            $msg=trans('words.fav_success');
        }  
          
        $response[]=array("success"=>$success,"msg"=>$msg);
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function post_continue_read()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id = $get_data['user_id'];
        $post_id = $get_data['post_id'];
        $page_num = addslashes($get_data['page_num']);
 
 
        $read_info = ContinueRead::where('post_id', '=', $post_id)->where('user_id', '=', $user_id)->first();   


        if($read_info)
        { 
            $read_obj = ContinueRead::findOrFail($read_info->id);        
            $read_obj->page_num = $page_num;
            $read_obj->save();

            $msg='Continue Read Update';
             
        }
        else
        {
            $read_obj = new ContinueRead;

            $read_obj->post_id = $post_id;
            $read_obj->user_id = $user_id;
            $read_obj->page_num = $page_num;             
            $read_obj->save();

            $msg='Continue Read Added';
        }  
          
        $response[]=array("msg"=>$msg);
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

     
    public function login()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

          
        $email=isset($get_data['email'])?$get_data['email']:'';
        $password=isset($get_data['password'])?$get_data['password']:'';
        
        if ($email=='' AND $password=='')
        {
                 
               $response[] = array('msg' => trans('words.email_pass_req'),'success'=>'0'); 

                return \Response::json(array(            
                    'EBOOK_APP' => $response,
                    'status_code' => 200,
                    'success' => 1
                ));
        }
 
        $user_info = User::where('email',$email)->first(); 

        if (!$user_info)
        {
                 
               $response[] = array('msg' => trans('words.email_password_invalid'),'success'=>'0'); 

                return \Response::json(array(            
                    'EBOOK_APP' => $response,
                    'status_code' => 200,
                    'success' => 1
                ));
        }
         
        if (Hash::check($password, $user_info['password'])) 
        {
           
            if($user_info->status==0){
                  
                  $response[] = array('msg' => trans('words.account_banned'),'success'=>'0');
            }             
            else
            { 
                $user_id=$user_info->id;
                $user = User::findOrFail($user_id);

                $user->touchLogin('app');

                 
                if($user->user_image!='')
                {
                    $user_image=user_image_url($user->user_image);
                }
                else
                {
                    $user_image=\URL::asset('upload/profile.png');
                }
                $phone= $user->phone?$user->phone:'';

                $response[] = array('user_id' => $user_id,'name' => $user->name,'username' => $user->username ?: '','email' => $user->email,'phone' => $phone,'user_image' => $user_image,'msg' => trans('words.login_success'),'success'=>'1');
            }

        }
        else
        {
            $response[] = array('msg' => trans('words.email_password_invalid'),'success'=>'0');
        }
        
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));   
    }

    public function signup()
    { 
        $get_data=checkSignSalt($_POST['data']);
            
        $name= $get_data['name'];
        $email= $get_data['email'];
        $password= $get_data['password'];
        $phone= $get_data['phone'];
        $department_id= isset($get_data['department_id']) ? $get_data['department_id'] : null;
        $university= isset($get_data['university']) ? $get_data['university'] : '';
        $college= isset($get_data['college']) ? $get_data['college'] : '';
        $rollnumber= isset($get_data['rollnumber']) ? $get_data['rollnumber'] : '';
        $gender= isset($get_data['gender']) ? $get_data['gender'] : '';
        
        $check_email = User::where('email', $email)->first();

        if($check_email)
        {
            $response[] = array('msg' => trans('words.email_already_used'),'success'=>'0'); 

                return \Response::json(array(            
                    'EBOOK_APP' => $response,
                    'status_code' => 200,
                    'success' => 1
                ));
        }
        else
        {   
            $user = new User;

            $user->usertype = 'User';
            $user->registered_via = 'app';
            $user->name = $name; 
            $user->username = generate_username($name, $email);
            $user->email = $email;         
            $user->password= bcrypt($password);  
            $user->phone= $phone?$phone:'';        
            $user->department_id= $department_id?$department_id:null;
            $user->university= $university?$university:'';
            $user->college= $college?$college:'';
            $user->rollnumber= $rollnumber?$rollnumber:'';
            $user->gender= $gender?$gender:'';
            $user->save();

            $response[] = array('msg' => trans('words.account_created_successfully'),'success'=>'1');
            return \Response::json(array(            
                'EBOOK_APP' => $response,
                'status_code' => 200,
                'success' => 1
            ));
        }
    }

    public function university_list()
    {
        $data_list = \App\University::where('status', 1)->orderBy('university_name')->get();

        $response = array();
        foreach ($data_list as $obj_data) {
            $response[] = array(
                'university_id'   => $obj_data->id,
                'university_name' => stripslashes($obj_data->university_name),
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'status_code' => 200,
            'success'     => 1
        ));
    }

    public function department_list()
    {
        $get_data = isset($_POST['data']) ? checkSignSalt($_POST['data']) : array();
        $university_id = isset($get_data['university_id']) ? $get_data['university_id'] : '';

        $query = \App\Department::where('status', 1);
        if (!empty($university_id)) {
            $query->where('university_id', $university_id);
        }
        $data_list = $query->orderBy('department_name')->get();

        $response = array();
        foreach ($data_list as $obj_data) {
            $response[] = array(
                'department_id'   => $obj_data->id,
                'department_name' => stripslashes($obj_data->department_name),
                'university_id'   => $obj_data->university_id,
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'status_code' => 200,
            'success'     => 1
        ));
    }

    public function college_list()
    {
        $data_list = \App\College::where('status', 1)->orderBy('college_name')->get();

        $response = array();
        foreach ($data_list as $obj_data) {
            $response[] = array(
                'college_id'   => $obj_data->id,
                'college_name' => stripslashes($obj_data->college_name),
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'status_code' => 200,
            'success'     => 1
        ));
    }

    public function user_upload_book(Request $request)
    {
        $get_data = checkSignSalt($_POST['data']);

        $user_id     = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $title       = isset($get_data['title']) ? $get_data['title'] : '';
        $description = isset($get_data['description']) ? $get_data['description'] : '';
        $cat_id      = isset($get_data['cat_id']) ? $get_data['cat_id'] : '';
        $url_type    = isset($get_data['url_type']) ? $get_data['url_type'] : ''; // 'server_url' (link) or 'local' (file)
        $book_url    = isset($get_data['book_url']) ? $get_data['book_url'] : '';

        // Basic validation
        if (empty($user_id) || empty($title) || empty($cat_id) || empty($url_type)) {
            $response[] = array('msg' => 'Missing required fields', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $user_obj = User::find($user_id);
        if (!$user_obj) {
            $response[] = array('msg' => 'Invalid user', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        // --- Cover image: no custom image on user uploads ---
        // Users don't upload a cover, and we intentionally do NOT auto-generate a
        // server-side image. We store the placeholder; the app renders the cover
        // locally (gradient from the category's cover_color + the title).
        $image_path = 'upload/book_placeholder.jpg';

        // --- Book file or link ---
        $final_url = '';
        if ($url_type == 'local') {
            $book_file = $request->file('book_file');
            if (!$book_file) {
                $response[] = array('msg' => 'No file uploaded', 'success' => '0');
                return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
            }
            // validate type + size (max 50 MB)
            $ext = strtolower($book_file->getClientOriginalExtension());
            $allowed = array('pdf', 'epub');
            if (!in_array($ext, $allowed)) {
                $response[] = array('msg' => 'Only PDF or EPUB files are allowed', 'success' => '0');
                return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
            }
            if ($book_file->getSize() > 52428800) {
                $response[] = array('msg' => 'File too large (max 50MB)', 'success' => '0');
                return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
            }
            $fileName = Str::slug(substr($title, 0, 40), '-') . '-' . md5(time() . $user_id) . '.' . $ext;
            // Store the book file on DigitalOcean Spaces under books/user_books/
            $final_url = spaces_upload($book_file, 'books/user_books', $fileName);
        } else {
            // link submission
            if (empty($book_url)) {
                $response[] = array('msg' => 'No link provided', 'success' => '0');
                return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
            }
            $final_url = $book_url;
        }

        // --- Create the pending book ---
        $book = new Books;
        $book->cat_id        = $cat_id;
        $book->book_access   = 'Free';
        $book->title         = addslashes($title);
        $book->description   = addslashes($description);
        $book->image         = $image_path;
        // Both local file uploads (now on Spaces) and link submissions store a full URL.
        $book->url_type      = 'server_url';
        $book->url           = $final_url;
        $book->download_enable = 0;
        $book->status        = 0;          // not live
        $book->uploaded_by   = $user_id;   // marks it as a user upload
        $book->upload_status = 'pending';  // moderation queue
        $book->save();

        $response[] = array('msg' => 'Book submitted for review. It will appear once approved.', 'book_id' => $book->id, 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    public function my_uploaded_books()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';

        $data_list = Books::where('uploaded_by', $user_id)->orderBy('id', 'DESC')->get();

        $response = array();
        foreach ($data_list as $obj_data) {
            $response[] = array(
                'post_id'       => $obj_data->id,
                'post_title'    => stripslashes($obj_data->title),
                'post_image'    => book_asset_url($obj_data->image),
                'cover_color'   => (\App\Category::getCategoryInfo($obj_data->cat_id, 'category_color') ?: '#4a7dff'),
                'upload_status' => $obj_data->upload_status,
                'reject_reason' => $obj_data->reject_reason,
            );
        }

        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    /* ===================== MEDIA FEED (photos + videos) ===================== */

    // Vertical scroll feed. Returns approved+live posts (both photos & videos),
    // newest first, paginated. Each item carries the uploader's display name.
    public function media_feed()
    {
        $get_data = checkSignSalt($_POST['data']);

        $page  = isset($get_data['page']) ? (int)$get_data['page'] : 1;
        $type  = isset($get_data['media_type']) ? $get_data['media_type'] : ''; // '' = all

        $query = MediaPost::where('status', 1)->where('upload_status', 'approved');
        if (in_array($type, ['photo', 'video'])) {
            $query->where('media_type', $type);
        }
        $data_list = $query->orderBy('id', 'DESC')->paginate($this->pagination_limit, ['*'], 'page', $page);

        $current_user = isset($get_data['user_id']) ? $get_data['user_id'] : 0;

        $response = array();
        foreach ($data_list as $obj) {
            $liked = 0;
            if ($current_user) {
                $liked = MediaLike::where('post_id', $obj->id)->where('user_id', $current_user)->count() > 0 ? 1 : 0;
            }
            $response[] = array(
                'post_id'        => $obj->id,
                'media_type'     => $obj->media_type,
                'title'          => stripslashes($obj->title),
                'description'    => stripslashes($obj->description),
                'link_url'       => $obj->link_url ?: '',
                'book_id'        => $obj->book_id ? (string)$obj->book_id : '',
                'book_title'     => $obj->book_id ? stripslashes((string)\App\Books::where('id', $obj->book_id)->value('title')) : '',
                'file_url'       => $obj->file_url,
                'thumb_url'      => $obj->thumb_url ?: '',
                'uploaded_by'    => $obj->uploaderName(),
                'is_admin'       => $obj->is_admin_upload ? '1' : '0',
                // feature toggles
                'show_views'     => $obj->show_views ? '1' : '0',
                'allow_likes'    => $obj->allow_likes ? '1' : '0',
                'allow_comments' => $obj->allow_comments ? '1' : '0',
                // counts + current-user state
                'view_count'     => (string)$obj->view_count,
                'like_count'     => (string)$obj->likesCount(),
                'comment_count'  => (string)$obj->commentsCount(),
                'is_liked'       => (string)$liked,
                'created_at'     => $obj->created_at ? $obj->created_at->format('Y-m-d H:i:s') : '',
            );
        }

        return \Response::json(array(
            'EBOOK_APP'    => $response,
            'total_pages'  => $data_list->lastPage(),
            'current_page' => $data_list->currentPage(),
            'status_code'  => 200,
            'success'      => 1,
        ));
    }

    // User uploads a photo or video. Verified users -> auto-approved & live.
    // Everyone else -> pending in the admin moderation queue.
    public function media_upload(Request $request)
    {
        $get_data = checkSignSalt($_POST['data']);

        $user_id     = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $media_type  = isset($get_data['media_type']) ? $get_data['media_type'] : 'photo';
        $title       = isset($get_data['title']) ? $get_data['title'] : '';
        $description = isset($get_data['description']) ? $get_data['description'] : '';

        if (empty($user_id)) {
            $response[] = array('msg' => 'Invalid user', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        if (!in_array($media_type, ['photo', 'video'])) {
            $response[] = array('msg' => 'Invalid media type', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $user_obj = User::find($user_id);
        if (!$user_obj) {
            $response[] = array('msg' => 'Invalid user', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $media_file = $request->file('media_file');
        if (!$media_file) {
            $response[] = array('msg' => 'No file uploaded', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $ext = strtolower($media_file->getClientOriginalExtension());
        $imageExts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $videoExts = array('mp4', 'mov', 'webm', 'm4v');

        if ($media_type == 'photo' && !in_array($ext, $imageExts)) {
            $response[] = array('msg' => 'Only JPG, PNG, GIF or WEBP images are allowed', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        if ($media_type == 'video' && !in_array($ext, $videoExts)) {
            $response[] = array('msg' => 'Only MP4, MOV or WEBM videos are allowed', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        // size cap: 10MB photos, 100MB videos
        $maxSize = ($media_type == 'video') ? 104857600 : 10485760;
        if ($media_file->getSize() > $maxSize) {
            $limit = ($media_type == 'video') ? '100MB' : '10MB';
            $response[] = array('msg' => 'File too large (max ' . $limit . ')', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $fileName = Str::slug(substr($title ?: 'media', 0, 40), '-') . '-' . md5(time() . $user_id) . '.' . $ext;
        $file_url = spaces_upload($media_file, 'media/' . $media_type . 's', $fileName);

        // optional thumbnail (for videos)
        $thumb_url = '';
        if ($request->hasFile('thumb_file')) {
            $thumb = $request->file('thumb_file');
            $textt = strtolower($thumb->getClientOriginalExtension());
            if (in_array($textt, $imageExts)) {
                $tName = 'thumb-' . md5(time() . $user_id) . '.' . $textt;
                $thumb_url = spaces_upload($thumb, 'media/thumbs', $tName);
            }
        }

        // Verified user -> auto approve; otherwise pending.
        $is_verified = isset($user_obj->is_verified) ? (int)$user_obj->is_verified : 0;

        $post = new MediaPost;
        $post->user_id        = $user_id;
        $post->media_type     = $media_type;
        $post->title          = addslashes($title);
        $post->description    = addslashes($description);
        $post->file_url       = $file_url;
        $post->thumb_url      = $thumb_url ?: null;
        $post->is_admin_upload = 0;
        $post->upload_status  = $is_verified ? 'approved' : 'pending';
        $post->status         = $is_verified ? 1 : 0;
        $post->save();

        $msg = $is_verified
            ? 'Uploaded successfully. It is now live.'
            : 'Uploaded. It will appear once approved by admin.';

        $response[] = array('msg' => $msg, 'post_id' => $post->id, 'auto_approved' => $is_verified ? '1' : '0', 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // The signed-in user's own uploads with their moderation status.
    public function my_uploaded_media()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';

        $data_list = MediaPost::where('user_id', $user_id)->orderBy('id', 'DESC')->get();

        $response = array();
        foreach ($data_list as $obj) {
            $response[] = array(
                'post_id'       => $obj->id,
                'media_type'    => $obj->media_type,
                'title'         => stripslashes($obj->title),
                'file_url'      => $obj->file_url,
                'thumb_url'     => $obj->thumb_url ?: '',
                'upload_status' => $obj->upload_status,
                'reject_reason' => $obj->reject_reason,
            );
        }

        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // A user can delete their own post.
    public function media_delete()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $post_id = isset($get_data['post_id']) ? $get_data['post_id'] : '';

        $post = MediaPost::where('id', $post_id)->where('user_id', $user_id)->first();
        if (!$post) {
            $response[] = array('msg' => 'Post not found', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        $post->delete();

        $response[] = array('msg' => 'Deleted', 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // Increment a post's view count (called as user scrolls onto it).
    public function media_view()
    {
        $get_data = checkSignSalt($_POST['data']);
        $post_id = isset($get_data['post_id']) ? $get_data['post_id'] : '';

        $post = MediaPost::find($post_id);
        if ($post) {
            $post->view_count = $post->view_count + 1;
            $post->save();
        }

        $response[] = array('msg' => 'ok', 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // Toggle a like on a post. Returns the new like state + count.
    public function media_like()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $post_id = isset($get_data['post_id']) ? $get_data['post_id'] : '';

        $post = MediaPost::find($post_id);
        if (!$post || empty($user_id)) {
            $response[] = array('msg' => 'Invalid request', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        if (!$post->allow_likes) {
            $response[] = array('msg' => 'Likes are disabled for this post', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $existing = MediaLike::where('post_id', $post_id)->where('user_id', $user_id)->first();
        if ($existing) {
            $existing->delete();
            $is_liked = 0;
        } else {
            $like = new MediaLike;
            $like->post_id = $post_id;
            $like->user_id = $user_id;
            $like->save();
            $is_liked = 1;
        }

        $response[] = array(
            'msg'        => 'ok',
            'is_liked'   => (string)$is_liked,
            'like_count' => (string)MediaLike::where('post_id', $post_id)->count(),
            'success'    => '1',
        );
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // Add a comment to a post.
    public function media_comment_add()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $post_id = isset($get_data['post_id']) ? $get_data['post_id'] : '';
        $comment = isset($get_data['comment']) ? trim($get_data['comment']) : '';

        $post = MediaPost::find($post_id);
        if (!$post || empty($user_id) || $comment == '') {
            $response[] = array('msg' => 'Invalid request', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        if (!$post->allow_comments) {
            $response[] = array('msg' => 'Comments are disabled for this post', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $c = new MediaComment;
        $c->post_id = $post_id;
        $c->user_id = $user_id;
        $c->comment = addslashes($comment);
        $c->status = 1;
        $c->save();

        $response[] = array(
            'msg'           => 'Comment added',
            'comment_id'    => $c->id,
            'comment_count' => (string)MediaComment::where('post_id', $post_id)->where('status', 1)->count(),
            'success'       => '1',
        );
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // List visible comments for a post (paginated), newest first.
    public function media_comment_list()
    {
        $get_data = checkSignSalt($_POST['data']);
        $post_id = isset($get_data['post_id']) ? $get_data['post_id'] : '';
        $page    = isset($get_data['page']) ? (int)$get_data['page'] : 1;

        $data_list = MediaComment::where('post_id', $post_id)->where('status', 1)
            ->orderBy('id', 'DESC')
            ->paginate($this->pagination_limit, ['*'], 'page', $page);

        $response = array();
        foreach ($data_list as $c) {
            $u = User::find($c->user_id);
            $response[] = array(
                'comment_id' => $c->id,
                'user_id'    => $c->user_id,
                'user_name'  => $u ? stripslashes($u->name) : 'User',
                'user_image' => ($u && $u->user_image) ? user_image_url($u->user_image) : '',
                'comment'    => stripslashes($c->comment),
                'created_at' => $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : '',
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'total_pages' => $data_list->lastPage(),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    // A user can delete their own comment.
    public function media_comment_delete()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $comment_id = isset($get_data['comment_id']) ? $get_data['comment_id'] : '';

        $c = MediaComment::where('id', $comment_id)->where('user_id', $user_id)->first();
        if (!$c) {
            $response[] = array('msg' => 'Comment not found', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }
        $post_id = $c->post_id;
        $c->delete();

        $response[] = array(
            'msg'           => 'Deleted',
            'comment_count' => (string)MediaComment::where('post_id', $post_id)->where('status', 1)->count(),
            'success'       => '1',
        );
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    // In-app notification list. Returns broadcasts (user_id null) + any
    // targeted at this user, newest first. Each carries the post_id to open.
    public function media_notification_list()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id = isset($get_data['user_id']) ? $get_data['user_id'] : '';
        $page    = isset($get_data['page']) ? (int)$get_data['page'] : 1;

        $data_list = MediaNotification::where(function ($q) use ($user_id) {
                $q->whereNull('user_id');
                if ($user_id) {
                    $q->orWhere('user_id', $user_id);
                }
            })
            ->orderBy('id', 'DESC')
            ->paginate($this->pagination_limit, ['*'], 'page', $page);

        $response = array();
        foreach ($data_list as $n) {
            // only surface notifications whose post is still live
            $post = MediaPost::find($n->post_id);
            if (!$post || $post->status != 1) {
                continue;
            }
            $response[] = array(
                'notification_id' => $n->id,
                'post_id'         => $n->post_id,
                'media_type'      => $post->media_type,
                'title'           => stripslashes($n->title),
                'message'         => stripslashes($n->message),
                'image'           => $n->image ?: '',
                'is_read'         => (string)$n->is_read,
                'created_at'      => $n->created_at ? $n->created_at->format('Y-m-d H:i:s') : '',
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'total_pages' => $data_list->lastPage(),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    // Mark a single notification (or all) as read for this user.
    public function media_notification_read()
    {
        $get_data = checkSignSalt($_POST['data']);
        $notification_id = isset($get_data['notification_id']) ? $get_data['notification_id'] : '';

        if ($notification_id == 'all') {
            MediaNotification::whereNull('user_id')->update(['is_read' => 1]);
        } else {
            $n = MediaNotification::find($notification_id);
            if ($n) {
                $n->is_read = 1;
                $n->save();
            }
        }

        $response[] = array('msg' => 'ok', 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    public function social_login()
    {   
        
        $get_data=checkSignSalt($_POST['data']);
            
        $login_type= $get_data['login_type']; // FB or Google
        $social_id= $get_data['social_id'];

        $name= $get_data['name'];
        $email= $get_data['email'];
        $password= bcrypt('123456dummy');
        $phone= '';
        
        $check_email = User::where('email', $email)->first();

        if($check_email)
        {
            $finduser = User::where('email', $email)->first();
        }
        else
        {
            if($login_type=="google")
            {
                 $finduser = User::where('google_id', $social_id)->first();
     
            }
            else
            {
                 $finduser = User::where('facebook_id', $social_id)->first();
      
            }
        }

        if($finduser)
        {   
                if($finduser->user_image!='')
                {
                    $user_image=user_image_url($finduser->user_image);
                }
                else
                {
                    $user_image=\URL::asset('upload/profile.png');
                }

                if($finduser->status==0){
                 
                  $response[] = array('msg' => trans('words.account_banned'),'success'=>'0');
                }
                else
                {
                 $phone= $finduser->phone?$finduser->phone:'';  
                 
                 if (empty($finduser->username)) {
                 $finduser->username = generate_username($finduser->name, $finduser->email);
                 $finduser->save();
                 }
                 $finduser->touchLogin('app');
                 $profile_complete = ($finduser->university && $finduser->department_id && $finduser->college) ? '1' : '0';
                 $response[] = array('user_id' => $finduser->id,'name' => $finduser->name,'username' => $finduser->username ?: '','email' => $finduser->email,'phone' => $phone,'user_image' => $user_image,'profile_complete' => $profile_complete,'msg' => trans('words.login_success'),'success'=>'1');

                }
        }
        else
        {

            if($login_type=="google")
            {
                 $social_login_type="google";
                 $google_id=$social_id;

                 $user_obj = new User;

                $user_obj->usertype = 'User';
                $user_obj->registered_via = 'app';
                $user_obj->social_login_type = $social_login_type; 
                $user_obj->google_id = $google_id; 
                $user_obj->name = $name; 
                $user_obj->email = $email;         
                $user_obj->password= bcrypt($password);  
                $user_obj->phone= $phone?$phone:'';    
                                // Save the Google profile photo as the avatar (full URL) if sent.
                if (!empty($get_data['user_image'])) {
                    $user_obj->user_image = $get_data['user_image'];
                }

                $user_obj->save();
     
            }
            else
            {
                 $social_login_type="facebook";
                 $facebook_id=$social_id;

                 $user_obj = new User;

                $user_obj->usertype = 'User';
                $user_obj->registered_via = 'app';
                $user_obj->social_login_type = $social_login_type; 
                $user_obj->facebook_id = $facebook_id; 
                $user_obj->name = $name; 
                $user_obj->email = $email;         
                $user_obj->password= bcrypt($password);  
                $user_obj->phone= $phone?$phone:'';        
                $user_obj->save();
      
            }

            //Get last insert user id
            $user_id=$user_obj->id;
 
            $user = User::findOrFail($user_id);

                 
            if($user->user_image!='')
            {
                $user_image=user_image_url($user->user_image);
            }
            else
            {
                $user_image=\URL::asset('upload/profile.png');
            }
            $phone= $user->phone?$user->phone:'';
            
            if (empty($user->username)) {
            $user->username = generate_username($user->name, $user->email);
            $user->save();
            }
            $user->touchLogin('app');
            // Brand-new social user: never has university/dept/college yet.
            $profile_complete = ($user->university && $user->department_id && $user->college) ? '1' : '0';
            $response[] = array('user_id' => $user_id,'name' => $user->name,'username' => $user->username ?: '','email' => $user->email,'phone' => $phone,'user_image' => $user_image,'profile_complete' => $profile_complete,'msg' => trans('words.login_success'),'success'=>'1');


            
        }

 
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));   
    }
     
 
    public function forgot_password()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $email=isset($get_data['email'])?$get_data['email']:'';
 
        $user = User::where('email', $email)->first();

        //dd($user);
        //exit;

        if(!$user)
        {
            $response[] = array('msg' => trans('words.email_not_found'),'success'=>'1');
        }
        else
        {  
           $user_id=$user->id;
           $name=$user->name;
           $email=$user->email;

           $password= Str::random(10);

           $user = User::findOrFail($user_id);
           $user->password= bcrypt($password);
           $user->save(); 
    
           try{

            $data_email = array(
                'name' => $name,
                'password' => $password
                );    

            \Mail::send('emails.password', $data_email, function($message) use ($name,$email){
                $message->to($email, $name)
                ->from(getcong('app_email'), getcong('app_name'))
                ->subject('Password Reset | '.getcong('app_name'));
            });    

            }catch (\Throwable $e) {
                     
                \Log::info($e->getMessage());    
            }     
     
           
           $response[] = array('msg' => trans('words.email_new_pass_sent'),'success'=>'1');
 
        }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

     
    public function profile()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        $user = User::where('id',$user_id)->first();

        if (!$user)
        {
                 
               $response[] = array('msg' => 'Something went wrong','success'=>'0'); 

                return \Response::json(array(            
                    'EBOOK_APP' => $response,
                    'status_code' => 200,
                    'success' => 1
                ));
        }
                 
        if($user->user_image!='')
        {
            $user_image=user_image_url($user->user_image);
        }
        else
        {
            $user_image=\URL::asset('upload/profile.jpg');
        }

        $phone=$user->phone?$user->phone:'';

        // Backfill: if this account has no username yet (older accounts, or rows the
        // one-time SQL missed), generate a unique one now and persist it, so the
        // profile always has a handle to show.
        if (empty($user->username)) {
            $user->username = generate_username($user->name, $user->email);
            $user->save();
        }

        // Resolve department name from the stored department_id (if any).
        $department_name = '';
        if (!empty($user->department_id)) {
            $dept = \App\Department::find($user->department_id);
            if ($dept) { $department_name = stripslashes($dept->department_name); }
        }

        $response[] = array(
            'user_id'       => $user_id,
            'name'          => $user->name,
            'username'      => $user->username ?: '',
            'email'         => $user->email,
            'phone'         => $phone,
            'user_image'    => $user_image,
            'university'    => isset($user->university) ? stripslashes($user->university) : '',
            'department_id' => isset($user->department_id) ? $user->department_id : '',
            'department'    => $department_name,
            'college'       => isset($user->college) ? stripslashes($user->college) : '',
            'gender'        => isset($user->gender) ? $user->gender : '',
            'rollnumber'    => isset($user->rollnumber) ? stripslashes($user->rollnumber) : '',
            'msg'           => trans('words.profile'),
            'success'       => '1'
        );


        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }
    
    public function result_get()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id  = $get_data['user_id'];

        $user = User::where('id', $user_id)->first();
        if (!$user) {
            $response[] = array('msg' => 'Something went wrong', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $result = Result::where('user_id', $user_id)->first();
        if (!$result) {
            $response[] = array('has_result' => 0, 'msg' => 'No result found', 'success' => '1');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $sems = array();
        foreach ($result->semesters as $sem) {
            $subs = array();
            foreach ($sem->subjects()->get() as $sub) {
                $subs[] = array(
                    'subject_code' => (string) $sub->subject_code,
                    'subject_name' => (string) $sub->subject_name,
                    'internal'     => $sub->internal,
                    'external'     => $sub->external,
                    'total'        => $sub->total,
                    'grade'        => (string) $sub->grade,
                    'credits'      => $sub->credits,
                    'is_backlog'   => (int) $sub->is_backlog,
                );
            }
            $sems[] = array(
                'sem_code'        => (string) $sem->sem_code,
                'sgpa'            => $sem->sgpa,
                'credits_earned'  => $sem->credits_earned,
                'exam_month_year' => (string) $sem->exam_month_year,
                'subjects'        => $subs,
            );
        }

        $latestCard = $result->reportCards()->orderBy('id', 'desc')->first();

        $response[] = array(
            'has_result'       => 1,
            'result_id'        => $result->id,
            'hall_ticket_no'   => (string) $result->hall_ticket_no,
            'student_name'     => (string) $result->student_name,
            'regulation'       => (string) $result->regulation,
            'degree'           => (string) $result->degree,
            'branch'           => (string) $result->branch,
            'current_cgpa'     => $result->current_cgpa,
            'total_credits'    => $result->total_credits,
            'backlogs_count'   => (int) $result->backlogs_count,
            'verified'         => (int) $result->verified,
            'locked'           => (int) $result->locked,   // app disables edit when 1
            'share_url'        => $result->share_token
                                    ? rtrim(env('APP_URL'), '/') . '/report/' . $result->share_token
                                    : '',
            'report_image_url' => $latestCard ? (string) $latestCard->pdf_url : '',
            'semesters'        => $sems,
            'msg'              => 'OK',
            'success'          => '1',
        );

        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    /**
     * result_save — create/replace the caller's own result (owner-only).
     * Rejects writes when locked (admin-verified). See SQL patch for schema.
     * Payload includes semesters_json (see ANDROID guide for exact shape).
     */
    public function result_save()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id  = $get_data['user_id'];

        $user = User::where('id', $user_id)->first();
        if (!$user) {
            $response[] = array('msg' => 'Something went wrong', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $hall = trim(isset($get_data['hall_ticket_no']) ? $get_data['hall_ticket_no'] : '');
        if ($hall === '') {
            $response[] = array('msg' => 'Hall ticket number is required', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        // Hall-ticket ownership guard.
        $existing = Result::where('hall_ticket_no', $hall)->first();
        if ($existing && $existing->user_id && (int) $existing->user_id !== (int) $user_id) {
            $response[] = array('msg' => 'This hall ticket is already registered by another account', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        // SERVER-SIDE LOCK ENFORCEMENT: verified records can't be edited by student.
        $result = $existing ?: Result::where('user_id', $user_id)->first();
        if ($result && (int) $result->locked === 1) {
            $response[] = array('msg' => 'This result has been verified and can no longer be edited', 'success' => '0', 'locked' => 1);
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $semJson = isset($get_data['semesters_json']) ? $get_data['semesters_json'] : '[]';
        $sems    = json_decode($semJson, true);
        if (!is_array($sems)) { $sems = array(); }

        \DB::beginTransaction();
        try {
            if (!$result) {
                $result = new Result();
                $result->hall_ticket_no = $hall;
                $result->user_id        = $user_id;
                $result->source         = 'manual';
                $result->verified       = 0;
                $result->locked         = 0;
                $result->is_public      = 0;
                $result->share_token    = \Illuminate\Support\Str::random(24);
            }

            $result->university_id  = isset($user->university_id) ? $user->university_id : $result->university_id;
            $result->student_name   = isset($get_data['student_name'])  ? $get_data['student_name']  : $user->name;
            $result->regulation     = isset($get_data['regulation'])    ? $get_data['regulation']    : $result->regulation;
            $result->degree         = isset($get_data['degree'])        ? $get_data['degree']        : $result->degree;
            $result->branch         = isset($get_data['branch'])        ? $get_data['branch']        : $result->branch;
            $result->current_cgpa   = isset($get_data['current_cgpa'])  ? $get_data['current_cgpa']  : null;
            $result->total_credits  = isset($get_data['total_credits']) ? $get_data['total_credits'] : null;
            $result->backlogs_count = isset($get_data['backlogs_count'])? (int) $get_data['backlogs_count'] : 0;
            $result->save();

            // Replace the whole tree.
            $oldSemIds = ResultSemester::where('result_id', $result->id)->pluck('id')->toArray();
            if (!empty($oldSemIds)) {
                ResultSubject::whereIn('result_semester_id', $oldSemIds)->delete();
                ResultSemester::where('result_id', $result->id)->delete();
            }

            foreach ($sems as $s) {
                $sem = new ResultSemester();
                $sem->result_id       = $result->id;
                $sem->sem_code        = isset($s['sem_code']) ? $s['sem_code'] : '';
                $sem->sgpa            = isset($s['sgpa']) ? $s['sgpa'] : null;
                $sem->credits_earned  = isset($s['credits_earned']) ? $s['credits_earned'] : null;
                $sem->exam_month_year = isset($s['exam_month_year']) ? $s['exam_month_year'] : '';
                $sem->save();

                $subjects = isset($s['subjects']) && is_array($s['subjects']) ? $s['subjects'] : array();
                foreach ($subjects as $sub) {
                    $row = new ResultSubject();
                    $row->result_semester_id = $sem->id;
                    $row->subject_code = isset($sub['subject_code']) ? $sub['subject_code'] : '';
                    $row->subject_name = isset($sub['subject_name']) ? $sub['subject_name'] : '';
                    $row->internal     = isset($sub['internal']) ? $sub['internal'] : null;
                    $row->external     = isset($sub['external']) ? $sub['external'] : null;
                    $row->total        = isset($sub['total']) ? $sub['total'] : null;
                    $row->grade        = isset($sub['grade']) ? $sub['grade'] : '';
                    $row->credits      = isset($sub['credits']) ? $sub['credits'] : null;
                    $row->is_backlog   = isset($sub['is_backlog']) ? (int) $sub['is_backlog'] : 0;
                    $row->save();
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $response[] = array('msg' => 'Could not save result', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $response[] = array('result_id' => $result->id, 'msg' => 'Result saved', 'success' => '1');
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    /**
     * report_generate — build the watermarked PNG report card for the caller's
     * own result, upload to Spaces, record in report_cards, return the URL.
     */
    public function report_generate()
    {
        $get_data = checkSignSalt($_POST['data']);
        $user_id  = $get_data['user_id'];

        $result = Result::where('user_id', $user_id)->first();
        if (!$result) {
            $response[] = array('msg' => 'No result to generate', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        // Shared generator lives in helpers.php (also used by admin verify).
        $url = generate_result_report_card($result);
        if (!$url) {
            $response[] = array('msg' => 'Could not generate report card', 'success' => '0');
            return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
        }

        $response[] = array(
            'report_image_url' => $url,
            'share_url'        => rtrim(env('APP_URL'), '/') . '/report/' . $result->share_token,
            'verified'         => (int) $result->verified,
            'msg'              => 'Report card ready',
            'success'          => '1',
        );
        return \Response::json(array('EBOOK_APP' => $response, 'status_code' => 200, 'success' => 1));
    }

    public function profile_update(Request $request)
    { 
         //$data =  \Request::except(array('_token'));
         
        $inputs = $request->all();
         //dd($inputs);
        //exit;
        //echo $inputs['data'];
        $get_data=checkSignSalt($inputs['data']);

          
        $user_id=$get_data['user_id'];    
        $user_obj = User::findOrFail($user_id);

        $icon = $request->file('user_image');
        
                 
        if($icon){

            // Remove previous Spaces object if the stored value is a Spaces URL.
            if ($user_obj->user_image && Str::startsWith($user_obj->user_image, ['http://', 'https://'])) {
                $oldKey = ltrim(parse_url($user_obj->user_image, PHP_URL_PATH), '/');
                if ($oldKey) { Storage::disk('spaces')->delete($oldKey); }
            } else {
                // Legacy local file cleanup.
                \File::delete(public_path('/upload/').$user_obj->user_image);
            }

            $hardPath =  Str::slug($get_data['name'], '-').'-'.md5(time());

            $img = Image::make($icon)->fit(250, 250)->encode('jpg', 90);
            $key = 'images/users/'.$hardPath.'-b.jpg';
            Storage::disk('spaces')->put($key, (string) $img, 'public');

            // Store the full CDN URL.
            $user_obj->user_image = Storage::disk('spaces')->url($key);
        }
        
        
        $user_obj->name = $get_data['name'];          
        $user_obj->email = $get_data['email']; 
        $user_obj->phone = $get_data['phone'];
        
        
        // Optional academic fields (used by the Google "complete profile" flow and
        // the edit-profile screen). Only overwrite when a value is sent.
        if (isset($get_data['university']) && $get_data['university'] !== '') {
            $user_obj->university = $get_data['university'];
        }
        if (isset($get_data['department_id']) && $get_data['department_id'] !== '') {
            $user_obj->department_id = $get_data['department_id'];
        }
        if (isset($get_data['college']) && $get_data['college'] !== '') {
            $user_obj->college = $get_data['college'];
        }

        
        if($get_data['password'])
        {
            $user_obj->password = bcrypt($get_data['password']);
        }         
       
        $user_obj->save();

        $user_id=$get_data['user_id'];    
        $user = User::findOrFail($user_id);

        if($user->user_image!='')
        {
            $user_image=user_image_url($user->user_image);
        }
        else
        {
            $user_image=\URL::asset('upload/profile.jpg');
        }


        $response[] = array('user_image' => $user_image,'msg' => trans('words.successfully_updated'),'success'=>'1');
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

    public function user_favourite_post_list()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        
        $data_list= Favourite::where('post_type','Book')->where('user_id',$user_id)->orderby('id','DESC')->paginate($this->pagination_limit);
        $total_records= Favourite::where('post_type','Book')->where('user_id',$user_id)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {

            foreach($data_list as $obj_data)
            { 
                 
                    $post_id=$obj_data->post_id;
                    $post_title= Books::getBookInfo($post_id,'title');
                    $post_image= book_asset_url(Books::getBookInfo($post_id,'image')); 
                     
                    $total_views= post_views_count($post_id,"Book");
                    $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                    $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");
                    
                          

                $response[]=array("post_id"=>$post_id,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite); 
                 
            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function user_download_list()
    {   
        $get_data=checkSignSalt($_POST['data']);

        $user_id= isset($get_data['user_id'])?$get_data['user_id']:"";

        $down_books= UserDownload::where('user_id',$user_id)->orderby('id','DESC')->get();

        if(count($down_books) > 0)
        {
            foreach($down_books as $down_data)
            {   
                $post_id= $down_data->post_id;

                $book_info = Books::where('id',$post_id)->where('status',1)->first();

                if($book_info)
                {
                    $post_access= $book_info->book_access;
                    $post_title= $book_info->title;
                    $post_image=book_asset_url($book_info->image);

                    $response[]=array("post_id"=>$post_id,"post_access"=>$post_access,"post_title"=>stripslashes($post_title),"post_image"=>$post_image);
                }
                
            }

            if(!isset($response))
            {
                $response=array();
            }

        }
        else
        {
            $response=array();
        }

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function user_rent_list()
    {   
        
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];
        
        $data_list= RentInfo::where('user_id',$user_id)->orderby('id','DESC')->paginate($this->pagination_limit);
        $total_records= RentInfo::where('user_id',$user_id)->count();

        if(count($data_list) > 0 AND $total_records > 0)
        {

            foreach($data_list as $obj_data)
            { 
                 
                    $post_id=$obj_data->rent_id;
                    $post_title= Books::getBookInfo($post_id,'title');
                    $post_image= book_asset_url(Books::getBookInfo($post_id,'image')); 
                      
                    $total_views= post_views_count($post_id,"Book");
                    $total_rate= PostRatings::getPostTotalRatings($post_id,"Book");  
                    $favourite=check_favourite("Book",$post_id,isset($get_data['user_id'])?$get_data['user_id']:"");
                    
                    $rent_exp_date = date('M d, Y',$obj_data->rent_exp_date);
                           
                $response[]=array("post_id"=>$post_id,"post_title"=>stripslashes($post_title),"post_image"=>$post_image,"views"=>$total_views,"total_rate"=>$total_rate,"favourite"=>$favourite,"rent_exp_date"=>$rent_exp_date); 
                 
            }
        }
        else
        {
            $response=array();
        }
 
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'total_records' => $total_records,
            'status_code' => 200,
            'success' => 1
        ));

    }

    public function user_reports()
    { 
         
        $get_data=checkSignSalt($_POST['data']);

        $user_id = $get_data['user_id'];
        $post_id = $get_data['post_id'];
        $post_type = $get_data['post_type'];

        $review_id = isset($get_data['review_id'])?$get_data['review_id']:0;

        $message = $get_data['message'];
 
         
            $re_obj = new Reports;

            $re_obj->post_type = $post_type;
            $re_obj->post_id = $post_id;
            $re_obj->user_id = $user_id;
            $re_obj->review_id = $review_id;
            $re_obj->message = $message;
            $re_obj->date = strtotime(date('m/d/Y H:i:s'));
            $re_obj->save();

            $success=true;
            
        $response[]=array("msg"=>trans('words.reports_success'));
         
         return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
        
    }
 

    public function check_user_plan()
    {
        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id'];

        $user_info = User::findOrFail($user_id);
        $user_plan_id=$user_info->plan_id;
        $user_plan_exp_date=$user_info->exp_date;
 

        if($user_plan_id==0)
        {          
            //\Session::flash('flash_message', 'Login status reset!');
            $response = array('msg' => trans('words.select_sub_plan'),'success'=>'0');

            return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
            ));
        }
        else if(strtotime(date('m/d/Y'))>$user_plan_exp_date)
        {

                $current_plan=SubscriptionPlan::getSubscriptionPlanInfo($user_plan_id,'plan_name');
                
                $expired_on=date('d, M Y',$user_plan_exp_date);

                $response = array('current_plan'=>$current_plan,'expired_on'=>$expired_on,'msg' => trans('words.renew_sub_plan'),'success'=>'0');

                return \Response::json(array(            
                'EBOOK_APP' => $response,
                'status_code' => 200,
                'success' => 1
                ));
        }
        else
        {
                $current_plan=SubscriptionPlan::getSubscriptionPlanInfo($user_plan_id,'plan_name');
                
                $expired_on=date('d, M Y',$user_plan_exp_date);

                $response = array('current_plan'=>$current_plan,'expired_on'=>$expired_on,'msg' => trans('words.my_subscription'),'success'=>'1');

                return \Response::json(array(            
                'EBOOK_APP' => $response,
                'status_code' => 200,
                'success' => 1
                ));
        }        
        
        
    }

    public function subscription_plan()
    {
        $get_data=checkSignSalt($_POST['data']);

        $plan_list = SubscriptionPlan::where('status','1')->orderby('id')->get(); 


        $settings = Settings::findOrFail('1'); 

        $currency_code=$settings->currency_code;
        
        if(count($plan_list) > 0)
        {
            foreach($plan_list as $plan_data)
            {
                    $plan_id= $plan_data->id;
                    $plan_name= $plan_data->plan_name;  
                    $plan_duration= SubscriptionPlan::getPlanDuration($plan_data->id);
                    $plan_price= $plan_data->plan_price;                 
                    
                    $response[]=array("plan_id"=>$plan_id,"plan_name"=>$plan_name,"plan_duration"=>$plan_duration,"plan_price"=>$plan_price,"currency_code"=>$currency_code);   
            }    
        }
        else
        {
            $response=array();
        }

        

        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

     
    public function transaction_add()
    {
        $get_data=checkSignSalt($_POST['data']);

        $plan_id=$get_data['plan_id'];
        $user_id=$get_data['user_id'];
        $payment_id=$get_data['payment_id'];
        $payment_gateway=$get_data['payment_gateway'];

        $plan_info = SubscriptionPlan::where('id',$plan_id)->where('status','1')->first();
        $plan_name=$plan_info->plan_name;
        $plan_days=$plan_info->plan_days;
        $plan_amount=$plan_info->plan_price;

        //User info update        
           
        $user = User::findOrFail($user_id);

        $user_email=$user->email;

        $user->plan_id = $plan_id;                    
        $user->start_date = strtotime(date('m/d/Y'));             
        $user->exp_date = strtotime(date('m/d/Y', strtotime("+$plan_days days")));
                   
        $user->plan_amount = $plan_amount;         
        $user->save();

        //Check duplicate
        $trans_info = Transactions::where('user_id',$user_id)->where('payment_id',$payment_id)->first();

        if($trans_info=="")
        {
            //Transactions info update
            $payment_trans = new Transactions;

            $payment_trans->user_id = $user_id;
            $payment_trans->email = $user_email;
            $payment_trans->plan_id = $plan_id;
            $payment_trans->gateway = $payment_gateway;
            $payment_trans->payment_amount = $plan_amount;
            $payment_trans->payment_id = $payment_id;
            $payment_trans->date = strtotime(date('m/d/Y H:i:s'));                    
            $payment_trans->save();
        }

        $response[] = array('msg' => trans('words.payment_success'),'success'=>'1');
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }

    public function transaction_rent_add()
    {
        $get_data=checkSignSalt($_POST['data']);

        $rent_id=$get_data['rent_id'];
        $user_id=$get_data['user_id'];
        $payment_id=$get_data['payment_id'];
        $payment_gateway=$get_data['payment_gateway'];

        $obj_data= Books::where('id',$rent_id)->first();
        $rent_title=$obj_data->plan_name;
        $rent_days=$obj_data->book_rent_time;
        $rent_amount=$obj_data->book_rent_price;

        //User info        
           
        $user = User::findOrFail($user_id);
        $user_email=$user->email;
        
        $check_rent_user=RentInfo::where('user_id',$user_id)->where('rent_id',$rent_id)->where('rent_type','Book')->first();
            
        $rent_time_end=strtotime(date('m/d/Y H:i:s', strtotime(" +$rent_days Days")));    
        
        if($check_rent_user)
        {   
            $rid=$check_rent_user->id;

            $rent_trans = RentInfo::findOrFail($rid);                 
            $rent_trans->rent_date = strtotime(date('m/d/Y H:i:s'));
            $rent_trans->rent_exp_date = $rent_time_end;
            $rent_trans->save();
        }
        else
        {
            $rent_trans = new RentInfo;
            $rent_trans->user_id = $user_id;
            $rent_trans->rent_id = $rent_id;
            $rent_trans->rent_type = 'Book';
            $rent_trans->rent_date = strtotime(date('m/d/Y H:i:s'));
            $rent_trans->rent_exp_date = $rent_time_end;
            $rent_trans->save();
        }

        //Check duplicate
        $trans_info = Transactions::where('user_id',$user_id)->where('payment_id',$payment_id)->first();

        if($trans_info=="")
        {
            //Transactions info update
            $payment_trans = new Transactions;

            $payment_trans->user_id = $user_id;
            $payment_trans->email = $user_email;
            $payment_trans->rent_id = $rent_id;
            $payment_trans->gateway = $payment_gateway;
            $payment_trans->payment_amount = $rent_amount;
            $payment_trans->payment_id = $payment_id;
            $payment_trans->date = strtotime(date('m/d/Y H:i:s'));                    
            $payment_trans->save();
        }

        $response[] = array('msg' => trans('words.payment_success'),'success'=>'1');
        
        return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200,
            'success' => 1
        ));
    }


    public function stripe_token_get()
    {

        $get_data=checkSignSalt($_POST['data']);

        $amount=$get_data['amount'];

 
        \Stripe\Stripe::setApiKey(getPaymentGatewayInfo(2,'stripe_secret_key'));


        $customer = \Stripe\Customer::create();
        $ephemeralKey = \Stripe\EphemeralKey::create(
            ['customer' => $customer->id],
            ['stripe_version' => '2020-08-27']
          );

        $currency_code=getcong('currency_code')?getcong('currency_code'):'USD';

        //$amount=10;

        $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => $currency_code,
            ]);

        if (!isset($intent->client_secret))
        {
            $response[]=array("msg"=>"The Stripe Token was not generated correctly",'success'=>'0');
        }
        else
        {
            $id = $intent->id;

            $client_secret = $intent->client_secret;
            $ephemeralKey = $ephemeralKey->secret;
            $customer_id = $customer->id;

            $response[]=array("id"=>$id,"stripe_payment_token"=>$client_secret,'ephemeralKey' =>$ephemeralKey,"customer" => $customer_id,"msg"=>"Stripe Token",'success'=>'1');
        }   
        

          return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
        ));  
    }

    public function get_braintree_token()
    {


        require_once(base_path() . '/public/paypal_braintree/lib/Braintree.php');

        $mode=getPaymentGatewayInfo(1,'mode');
        
        if($mode=="sandbox")
        {
            $environment='sandbox';
        }
        else
        {
            $environment='production';
        }


        $merchantId=getPaymentGatewayInfo(1,'braintree_merchant_id');
        $publicKey=getPaymentGatewayInfo(1,'braintree_public_key');
        $privateKey=getPaymentGatewayInfo(1,'braintree_private_key');
 

        $gateway = new \Braintree\Gateway([
                        'environment' => $environment,
                        'merchantId' => $merchantId,
                        'publicKey' => $publicKey,
                        'privateKey' => $privateKey
                        ]);


        $clientToken = $gateway->clientToken()->generate();

        $response[] = array('client_token' => $clientToken,'msg' => 'Token created','success'=>'1');

           return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
             ));

    }  

    public function braintree_checkout()
    {

        $get_data=checkSignSalt($_POST['data']); 
         
        require_once(base_path() . '/public/paypal_braintree/lib/Braintree.php');

        $mode=getPaymentGatewayInfo(1,'mode');
        
        if($mode=="sandbox")
        {
            $environment='sandbox';
        }
        else
        {
            $environment='production';
        }
 

        $merchantId=getPaymentGatewayInfo(1,'braintree_merchant_id');
        $publicKey=getPaymentGatewayInfo(1,'braintree_public_key');
        $privateKey=getPaymentGatewayInfo(1,'braintree_private_key');
        $merchantAccountId=getPaymentGatewayInfo(1,'braintree_merchant_account_id');

        $gateway = new \Braintree\Gateway([
                        'environment' => $environment,
                        'merchantId' => $merchantId,
                        'publicKey' => $publicKey,
                        'privateKey' => $privateKey
                        ]);

        $payment_amount=$get_data['payment_amount'];
        $payment_nonce=$get_data['payment_nonce'];

        $result = $gateway->transaction()->sale([
          'amount' => $payment_amount,
          'paymentMethodNonce' => $payment_nonce,
          'merchantAccountId' => $merchantAccountId,
          'options' => [
            'submitForSettlement' => True
          ]
        ]);

        //echo $result->transaction->id;
        
        //dd($result);exit;

        if ($result->success) {

            $paypal_payment_id = $result->transaction->paypal['paymentId'];

            $transaction_id= $result->transaction->id;

            $response[] = array('transaction_id' => $transaction_id,'paypal_payment_id' => $paypal_payment_id,'msg' => 'Transaction successful','success'=>'1');

        }
        else
        {
            $response[] = array('msg' => 'Transaction failed','success'=>'0');
        }

           return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
             ));

    }

    public function razorpay_order_id_get()
    {

        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data["user_id"];
        $amount=$get_data['amount']; 

        $razor_key=getPaymentGatewayInfo(3,'razorpay_key');
        $razor_secret=getPaymentGatewayInfo(3,'razorpay_secret');

        $api = new Api($razor_key, $razor_secret);

        $order = $api->order->create(array('receipt' => 'rcptid_'.$user_id, 'amount' => $amount, 'currency' => 'INR'));

        $orderId = $order['id'];
         
        $response[]=array("order_id"=>$orderId,"msg"=>"Order ID created",'success'=>'1');
         
          return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
        ));  
    }
    
    public function payumoney_hash_generator()
    {
        
        $get_data=checkSignSalt($_POST['data']); 

        $hashdata=$get_data["hashdata"];
        $salt=getPaymentGatewayInfo(6,'payu_salt');
         
        /***************** DO NOT EDIT ***********************/
        $payhash_str = $hashdata.$salt;

        
        $hash = strtolower(hash('sha512', $payhash_str));
        /***************** DO NOT EDIT ***********************/

        $response[]=array("payu_hash"=>$hash,"msg"=>"Hash created",'success'=>'1');
         
          return \Response::json(array(            
            'EBOOK_APP' => $response,
            'status_code' => 200
        ));
         
    }

    public function create_paytm_token()
    {

        $get_data=checkSignSalt($_POST['data']); 
         
        $paytmParams = array();

        $order_id=time();
        $plan_amount  = $get_data["amount"];
        $user_id   = "CUST_00".$get_data["user_id"];
        
        if(getPaymentGatewayInfo(9,'mode') == "live")
        {
          $merchant_id= getPaymentGatewayInfo(9,'paytm_merchant_id');
          $merchant_key= getPaymentGatewayInfo(9,'paytm_merchant_key');

          $websiteName= "DEFAULT";

          $initiate_url= "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".$merchant_id."&orderId=".$order_id;

          $callbackUrl= "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=".$order_id;
            
        }
        else
        {
          $merchant_id= getPaymentGatewayInfo(9,'paytm_merchant_id');
          $merchant_key= getPaymentGatewayInfo(9,'paytm_merchant_key');

          $websiteName= "WEBSTAGING";

          $initiate_url= "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".$merchant_id."&orderId=".$order_id;

          $callbackUrl= "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=".$order_id;

         }


        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => $merchant_id,
            "websiteName"   => $websiteName,
            "orderId"       => $order_id,
            "callbackUrl"   => $callbackUrl,
            "txnAmount"     => array(
                "value"     => $plan_amount,
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => $user_id,
            ),
        );

        $checksum = \PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $merchant_key);
 

        $paytmParams["head"] = array(
        "channelId"=> "WEB",
        "signature"=> $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        $url = $initiate_url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);

        $result=json_decode($response);
        $txnToken=$result->body->txnToken;

        $response_arr[] = array('txn_token'=>$txnToken,'order_id'=>$order_id,'msg' => 'Paytm token generated','success'=>'1');
            
         return \Response::json(array(            
            'EBOOK_APP' => $response_arr,
             'status_code' => 200
        ));

    }

    public function account_delete()
    { 

        $get_data=checkSignSalt($_POST['data']);

        $user_id=$get_data['user_id']; 

        $fav_obj = Favourite::where('user_id',$user_id)->delete();
        $rate_obj = PostRatings::where('user_id',$user_id)->delete();
        $rep_obj = Reports::where('user_id',$user_id)->delete();
        $read_obj = ContinueRead::where('user_id',$user_id)->delete();
        $down_obj = UserDownload::where('user_id',$user_id)->delete(); 
        
        $user = User::findOrFail($user_id);   
        $user->delete();

         //Account Delete Email
         if(getenv("MAIL_USERNAME"))
         {
             $user_name=$user->name;
             $user_email=$user->email;
 
             $data_email = array(
                 'name' => $user_name,
                 'email' => $user_email
                 );    
 
             \Mail::send('emails.account_delete', $data_email, function($message) use ($user_name,$user_email){
                 $message->to($user_email, $user_name)
                 ->from(getcong('app_email'), getcong('app_name'))
                 ->subject(trans('words.user_dlt_email_subject'));
             });    
         }

        $response_arr[] = array('msg' => trans('words.user_dlt_success'),'success'=>'1');
         
         return \Response::json(array(            
            'EBOOK_APP' => $response_arr,
            'status_code' => 200
        ));        
        
    }
 
}
