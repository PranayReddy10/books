<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str; 

class AdminController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');	
         
    }
 
    public function index()
    { 
        return view('admin.pages.dashboard');
    }
	
	public function profile()
    { 
        $page_title = trans('words.profile');

        return view('admin.pages.profile',compact('page_title'));
    }
    
    public function updateProfile(Request $request)
    {   
    	$id=Auth::user()->id;	 
    	$user = User::findOrFail($id);

	    $data =  \Request::except(array('_token')) ;
	    
	    $rule=array(
		        'name' => 'required',
		        'email' => 'required|email|max:255|unique:users,email,'.$id,
		        'user_image' => 'mimes:jpg,jpeg,gif,png'
		   		 );
	    
	   	 $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
	    

	    $inputs = $request->all();
		
		$icon = $request->file('user_image');
		
		         
        if($icon){
            $tmpFilePath = public_path('/upload/');

            $hardPath =  Str::slug($inputs['name'], '-').'-'.md5(time());

            $img = Image::make($icon);

            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            //$img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->user_image = $hardPath.'-b.jpg';
        }
        
		
		$user->name = $inputs['name'];          
		$user->email = $inputs['email']; 
		$user->phone = $inputs['phone'];
        
        if($inputs['password'])
        {
            $user->password = bcrypt($inputs['password']);
        }
         
	   
	    $user->save();

	    Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }
    
    public function updatePassword(Request $request)
    { 
    	 
    		//$user = User::findOrFail(Auth::user()->id);
		
		
		    $data =  \Request::except(array('_token')) ;
            $rule  =  array(
                    'password'       => 'required|confirmed',
                    'password_confirmation'       => 'required'
                ) ;
 
            $validator = \Validator::make($data,$rule);
 
            if ($validator->fails())
            {
                    return redirect()->back()->withErrors($validator->messages());
            }
		
	   		/* $val=$this->validate($request, [
                    'password' => 'required|confirmed',
            ]);  */      
         
	    $credentials = $request->only('password', 'password_confirmation'
            );
            
        $user = \Auth::user();
        $user->password = bcrypt($credentials['password']);
        $user->save();

	    Session::flash('flash_message', trans('words.successfully_updated'));

        return redirect()->back();
    }
	
      
     
}
