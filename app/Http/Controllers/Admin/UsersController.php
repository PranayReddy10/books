<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Department;
use App\University;
use App\College;
use App\Favourite;
use App\PostRatings;
use App\PostViewsDownload;
use App\Reports;
use App\SubscriptionPlan;
use App\Transactions;

use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image; 
use Illuminate\Support\Facades\DB;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str; 

class UsersController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');	
		
		 parent::__construct();
          
    }
    public function list()    { 
         
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        } 

        $page_title=trans('words.users');

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];  
            $user_list = User::where("usertype", "=","User")->where("name", "LIKE","%$keyword%")->orwhere("email", "LIKE","%$keyword%")->orderBy('id','DESC')->paginate(10);

            $user_list->appends(\Request::only('s'))->links();
        }         
        else
        {
          
            $user_list = User::where('usertype', '=', 'User')->orderBy('id','DESC')->paginate(10);
        }
         
        return view('admin.pages.users.list',compact('page_title','user_list'));
    } 
     
    public function add()    { 
        
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }

        $page_title=trans('words.add_user');

        $plan_list = SubscriptionPlan::where('status','1')->orderby('id')->get();
        $department_list = Department::where('status',1)->orderby('department_name')->get();
        $college_list = College::where('status',1)->orderby('college_name')->get();
        $university_list = University::where('status',1)->orderby('university_name')->get();
           
        return view('admin.pages.users.addedit',compact('page_title','plan_list','department_list','college_list','university_list'));
    }
    
    public function addnew(Request $request)
    { 
    	 
    	$data =  \Request::except(array('_token')) ;
	    
	    $inputs = $request->all();
	    
	    if(!empty($inputs['id']))
	    {
			$rule=array(
		        'name' => 'required',
		        'email' => 'required|email|max:255|unique:users,email,'.$inputs['id'],
                'user_image' => 'mimes:jpg,jpeg,gif,png' 
		   		 );
			
		}
		else
		{
			$rule=array(
		        'name' => 'required',
		        'email' => 'required|email|max:255|unique:users,email',
		        'password' => 'required|min:8|max:15',
                'user_image' => 'mimes:jpg,jpeg,gif,png'
		   		 );
		}
	    
	    
	    
	   	 $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
	      
		if(!empty($inputs['id'])){
           
            $user = User::findOrFail($inputs['id']);

        }else{

            $user = new User;

        }
		
        $icon = $request->file('user_image');        
                 
        if($icon){
            //$tmpFilePath = 'upload/';
            $tmpFilePath = public_path('/upload/');

            $hardPath =  Str::slug($inputs['name'], '-').'-'.md5(time());

            $img = Image::make($icon);

            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            //$img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->user_image = $hardPath.'-b.jpg';
        }  
		
        //Get Plan info 
        $plan_id=$inputs['subscription_plan'];
        $plan_info = SubscriptionPlan::where('id',$plan_id)->where('status','1')->first();        
        $plan_days=$plan_info->plan_days;
        
		$user->name = $inputs['name'];		 
		$user->email = $inputs['email'];

        // Assign a username for new users (or backfill if missing on edit).
        if(!empty($inputs['username']))
        {
            $user->username = $inputs['username'];
        }
        else if(empty($user->username))
        {
            $user->username = generate_username($inputs['name'], $inputs['email']);
        }
        
        if($inputs['password'])
        {
            $user->password= bcrypt($inputs['password']); 
        }        
        $user->phone = $inputs['phone'];
        $user->department_id = !empty($inputs['department_id']) ? $inputs['department_id'] : null;
        $user->university = isset($inputs['university']) ? $inputs['university'] : null;
        $user->rollnumber = isset($inputs['rollnumber']) ? $inputs['rollnumber'] : null;
        $user->college = isset($inputs['college']) ? $inputs['college'] : null;
        $user->gender = isset($inputs['gender']) ? $inputs['gender'] : null;
        
        if(empty($inputs['id']))
        {
            if($inputs['subscription_plan']!="")
            {
                $user->exp_date = strtotime(date('m/d/Y', strtotime("+$plan_days days")));

            }
            else
            {
                if($inputs['exp_date']!="")
                {
                    $user->exp_date = strtotime($inputs['exp_date']);
                }
                else
                {
                    $user->exp_date = null;
                }
            }
        }
        else
        {   
            if($inputs['exp_date']!="")
            {
                $user->exp_date = strtotime($inputs['exp_date']);
            }
            else
            {
                $user->exp_date = null;
            }
        }
         

        $user->plan_id = $plan_id;
         
        $user->status = $inputs['status'];
	    $user->save(); 
		
		if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }		     
        
         
    }     
    
    public function edit($id)    
    {     
    	if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }		
    	  $page_title=trans('words.edit_user');

          $user = User::findOrFail($id);
          
          $plan_list = SubscriptionPlan::where('status','1')->orderby('id')->get();
          $department_list = Department::where('status',1)->orderby('department_name')->get();
          $college_list = College::where('status',1)->orderby('college_name')->get();
          $university_list = University::where('status',1)->orderby('university_name')->get();
            
          return view('admin.pages.users.addedit',compact('page_title','user','plan_list','department_list','college_list','university_list'));
        
    }	 
    
    public function delete($id)
    {
    	
    	if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }
        
        $fav_obj = Favourite::where('user_id',$id)->delete();
        $rate_obj = PostRatings::where('user_id',$id)->delete();
        $rep_obj = Reports::where('user_id',$id)->delete();

        $user = User::findOrFail($id);         
		$user->delete();
		
        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();

    }    

    public function user_history($id)    
    {     
          if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }       
          $page_title=trans('words.user_history');

          $user = User::findOrFail($id);

          $user_id=$user->id;

          $transactions_list = Transactions::where('user_id',$user_id)->orderBy('id','DESC')->paginate(10);

          // Books this user uploaded (any status)
          $uploaded_books = \App\Books::where('uploaded_by',$user_id)->orderBy('id','DESC')->get();

          // Media posts this user uploaded (any status)
          $uploaded_posts = \App\MediaPost::where('user_id',$user_id)->orderBy('id','DESC')->get();

          // Books this user has read / is reading (continue_read tracking)
          $reading_history = \App\ContinueRead::where('user_id',$user_id)->orderBy('id','DESC')->get();
           
           
          return view('admin.pages.users.history',compact('page_title','user','transactions_list','uploaded_books','uploaded_posts','reading_history'));
        
    }

     
    public function user_export()    
    {
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

          return Excel::download(new UsersExport, 'users.csv');

    }
    
    //Sub Admin

    public function admin_list()    { 
         
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        } 

        $page_title=trans('words.admin_list');

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];  
            $user_list = User::where("usertype", "!=","User")->where('id', '!=', 1)->where("name", "LIKE","%$keyword%")->where("email", "LIKE","%$keyword%")->orderBy('id','DESC')->paginate(10);

            $user_list->appends(\Request::only('s'))->links();
        }        
        else
        {
          
            $user_list = User::where('usertype', '!=', 'User')->where('id', '!=', 1)->orderBy('id','DESC')->paginate(10);
        }
         
        return view('admin.pages.users.admin_list',compact('page_title','user_list'));
    } 

    public function admin_add(){ 
        
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

        $page_title=trans('words.add_admin');
        $roles_list = \App\Role::orderBy('name')->get();
        return view('admin.pages.users.addedit_admin',compact('page_title','roles_list'));
    }
    
    public function admin_addnew(Request $request)
    { 
         
        $data =  \Request::except(array('_token')) ;
        
        $inputs = $request->all();
        
        if(!empty($inputs['id']))
        {
            $rule=array(
                'name' => 'required',
                'email' => 'required|email|max:255|unique:users,email,'.$inputs['id'],
                'user_image' => 'mimes:jpg,jpeg,gif,png' 
                 );
            
        }
        else
        {
            $rule=array(
                'name' => 'required',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:8|max:15',
                'user_image' => 'mimes:jpg,jpeg,gif,png'
                 );
        }
        
        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
          
        if(!empty($inputs['id'])){
           
            $user = User::findOrFail($inputs['id']);

        }else{

            $user = new User;

        }
        
        $icon = $request->file('user_image');        
                 
        if($icon){
            $tmpFilePath = public_path('/upload/');

            $hardPath =  Str::slug($inputs['name'], '-').'-'.md5(time());

            $img = Image::make($icon);

            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            //$img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->user_image = $hardPath.'-b.jpg';
        }          
         

        $user->usertype = $inputs['usertype'];
        $user->name = $inputs['name'];       
        $user->email = $inputs['email'];

        // RBAC: assign the chosen role (only meaningful for Sub_Admin).
        if(isset($inputs['usertype']) && $inputs['usertype']=='Sub_Admin'){
            $user->role_id = !empty($inputs['role_id']) ? $inputs['role_id'] : null;
        } else {
            $user->role_id = null; // master admins don't need a role
        }

        if(empty($user->username))
        {
            $user->username = generate_username($inputs['name'], $inputs['email']);
        }
        
        if($inputs['password'])
        {
            $user->password= bcrypt($inputs['password']); 
        }        
        $user->phone = $inputs['phone'];
        $user->status = $inputs['status'];
        $user->save(); 
        
        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }            
        
         
    }

    public function admin_edit($id)    
    {     
          if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }       
          $page_title=trans('words.edit_admin');

          $user = User::findOrFail($id);
          $roles_list = \App\Role::orderBy('name')->get();

          return view('admin.pages.users.addedit_admin',compact('page_title','user','roles_list'));
        
    }

    public function admin_delete($id)
    {
        
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }
        
        if($id!=1)
        {
            $fav_obj = Favourite::where('user_id',$id)->delete();
            $rate_obj = PostRatings::where('user_id',$id)->delete();
            $rep_obj = Reports::where('user_id',$id)->delete();

            $user = User::findOrFail($id);         
            $user->delete();
        } 
          
        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();

    }  

    /**
     * Toggle a user's "verified uploader" status. Verified users' media
     * posts are auto-approved and skip the moderation queue.
     */
    public function verify_toggle($id)
    {
        if (Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $user = User::findOrFail($id);
        $user->is_verified = $user->is_verified ? 0 : 1;
        $user->save();

        \Session::flash('flash_message', $user->is_verified
            ? 'User verified. Their uploads will now auto-approve.'
            : 'User un-verified. Their uploads will require approval.');

        return redirect()->back();
    }
   
    	
}
