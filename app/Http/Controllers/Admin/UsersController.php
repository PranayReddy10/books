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
    public function list(Request $request)    { 
         
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        } 

        $page_title=trans('words.users');

        $filters = self::userFilters($request);
        $user_list = self::filteredUsers($filters)->orderBy('id','DESC')->paginate(10);
        $user_list->appends(array_filter($filters, 'strlen'));

        $universities = University::where('status',1)->orderBy('university_name')->pluck('university_name');
        $departments = Department::where('status',1)->orderBy('department_name')->get(['id','department_name']);
        $colleges = College::where('status',1)->orderBy('college_name')->pluck('college_name');

        return view('admin.pages.users.list',compact('page_title','user_list','filters','universities','departments','colleges'));
    } 

    /** The filters the users list understands, read from the query string. */
    public static function userFilters(Request $request)
    {
        $keys = ['s','university','department_id','college','gender','status','verified','signup','profile','joined'];
        $filters = [];
        foreach ($keys as $key) {
            $filters[$key] = trim((string) $request->query($key, ''));
        }
        return $filters;
    }

    /**
     * Students matching the list filters. Shared with the export, so the file
     * holds exactly the users the admin is looking at.
     */
    public static function filteredUsers(array $filters)
    {
        $query = User::where('usertype', 'User');
        $f = function ($key) use ($filters) { return isset($filters[$key]) ? $filters[$key] : ''; };

        if ($f('s') !== '') {
            $keyword = $f('s');
            // Grouped, so the OR cannot pull admins into the list.
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                  ->orWhere('email', 'LIKE', "%$keyword%")
                  ->orWhere('phone', 'LIKE', "%$keyword%")
                  ->orWhere('rollnumber', 'LIKE', "%$keyword%")
                  ->orWhere('username', 'LIKE', "%$keyword%");
            });
        }
        if ($f('university') !== '')    { $query->where('university', $f('university')); }
        if ($f('department_id') !== '') { $query->where('department_id', (int) $f('department_id')); }
        if ($f('college') !== '')       { $query->where('college', $f('college')); }
        if ($f('gender') !== '')        { $query->where('gender', $f('gender')); }
        if ($f('status') === 'active')   { $query->where('status', 1); }
        if ($f('status') === 'inactive') { $query->where('status', 0); }
        if ($f('verified') === 'yes')    { $query->where('is_verified', 1); }
        if ($f('verified') === 'no')     { $query->where('is_verified', 0); }
        if ($f('signup') === 'google')   { $query->where('social_login_type', 'google'); }
        if ($f('signup') === 'email')    {
            $query->where(function ($q) { $q->whereNull('social_login_type')->orWhere('social_login_type', ''); });
        }

        // Same rule the app uses to send Google users to "complete profile".
        $required = ['phone', 'gender', 'university', 'department_id', 'college'];
        if ($f('profile') === 'complete') {
            foreach ($required as $col) {
                $query->whereNotNull($col)->where($col, '!=', '');
            }
        }
        if ($f('profile') === 'incomplete') {
            $query->where(function ($q) use ($required) {
                foreach ($required as $col) {
                    $q->orWhereNull($col)->orWhere($col, '');
                }
            });
        }

        $since = ['today' => 'today', '7' => '-7 days', '30' => '-30 days', '90' => '-90 days'];
        if (isset($since[$f('joined')])) {
            $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($since[$f('joined')])));
        }

        return $query;
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

        if ($user->wasRecentlyCreated || $user->wasChanged('rollnumber') || $user->wasChanged('university')) {
            \App\Services\StudentResults::syncAfterResponse($user->id);
        }

		
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

          $transactions_list = Transactions::where('user_id',$user_id)->orderBy('id','DESC')->paginate(10)->fragment('tab-payments');

          // Books this user uploaded (any status)
          $uploaded_books = \App\Books::where('uploaded_by',$user_id)->orderBy('id','DESC')->get();

          // Media posts this user uploaded (any status)
          $uploaded_posts = \App\MediaPost::where('user_id',$user_id)->orderBy('id','DESC')->get();

          // Books this user has read / is reading (continue_read tracking)
          $reading_history = \App\ContinueRead::where('user_id',$user_id)->orderBy('id','DESC')->get();
           
           
          $total_spent = (float) Transactions::where('user_id',$user_id)->sum('payment_amount');

          // University results linked to this account (imported at sign-up for JNTU students).
          $result = \App\Result::where('user_id',$user_id)->first();
          if (!$result && trim((string) $user->rollnumber) !== '') {
              $result = \App\Result::where('hall_ticket_no', strtoupper(trim($user->rollnumber)))->first();
          }
          $results_pending = isset(\App\Services\StudentResults::pending()[$user_id]);
          $favourites_count = Favourite::where('user_id',$user_id)->count();
          $reviews_count = PostRatings::where('user_id',$user_id)->count();

          // What sign-up requires that this account still lacks.
          $missing = [];
          foreach (['phone' => 'Phone', 'gender' => 'Gender', 'university' => 'University', 'department_id' => 'Department', 'college' => 'College'] as $col => $label) {
              if (trim((string) $user->$col) === '') { $missing[] = $label; }
          }

          return view('admin.pages.users.history',compact('page_title','user','transactions_list','uploaded_books','uploaded_posts','reading_history','total_spent','favourites_count','reviews_count','missing','result','results_pending'));
        
    }

     
    /** Pull this student's JNTU results now instead of waiting for the retry. */
    public function sync_results($id)
    {
        $user = User::findOrFail($id);
        $state = (new \App\Services\StudentResults())->sync($user);

        $messages = [
            'ready'    => 'Results imported for ' . $user->rollnumber,
            'linked'   => 'Results linked for ' . $user->rollnumber,
            'queued'   => 'The university is still fetching ' . $user->rollnumber . '. It will be retried automatically.',
            'conflict' => 'Hall ticket ' . $user->rollnumber . ' is already linked to another account.',
            'skipped'  => 'Nothing to fetch: automatic results need a JNTU university and a valid hall ticket, and published marks.',
            'error'    => 'Could not reach the results service. It will be retried automatically.',
        ];
        \Session::flash('flash_message', $messages[$state] ?? $state);

        return redirect('admin/users/history/' . $user->id);
    }

    public function user_export()    
    {
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
            
        }

          return Excel::download(new UsersExport(self::userFilters(request())), 'users-'.date('Y-m-d').'.xlsx');

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
