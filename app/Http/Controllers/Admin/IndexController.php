<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class IndexController extends MainAdminController
{
	
    public function index()
    {   
        if(!alreadyInstalled()){
            return redirect('public/install');
        }

    	if (Auth::check()) {
                        
            return redirect('admin/dashboard'); 
        }
 
        return view('admin.index');
    }
	
	/**
     * Do user login
     * @return $this|\Illuminate\Http\RedirectResponse
     */
	 
    public function postLogin(Request $request)
    {
    	
   
      $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);


        $credentials = $request->only('email', 'password');


		$remember_me = $request->has('remember') ? true : false; 
		
         if (Auth::attempt($credentials, $remember_me)) {

            if(Auth::user()->status=='0'){
                \Auth::logout();
                return redirect('/admin')->withErrors(trans('words.account_banned'));
            }

            return $this->handleUserWasAuthenticated($request);
        }
 
       return redirect('/admin')->withErrors(trans('words.email_password_invalid'));
        
    }
    
     /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request)
    {

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }
 
        return redirect('admin/dashboard'); 
    }
    
    
    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return redirect('admin/');
    }


    public function forgot_password()    
    {      
          return view('admin.forgot_password');        
    }

    public function forgot_password_send(Request $request)
    {  
       
       $data =  \Request::except(array('_token')) ;
         
       $rule=array(
         'email' => 'required'
          );
        
        $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        
        $inputs = $request->all();
         
        $email=isset($inputs['email'])?$inputs['email']:'';
 
        $user = User::where('email', $email)->first();

       
        if(!$user)
        {
 
            \Session::flash('error_flash_message', trans('words.email_not_found'));

            return \Redirect::back();
        }
        else
        {  
           $user_id=$user->id;
           $name=$user->name;
           $email=$user->email;

           $password= Str::random(10);

           // Email FIRST. Only persist the new password if the mail actually goes
           // out, so a mail failure can never lock the user out of their account.
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

            // Mail sent — now it's safe to save the new password.
            $user = User::findOrFail($user_id);
            $user->password= bcrypt($password);
            $user->save();

            \Session::flash('flash_message', trans('words.email_new_pass_sent'));
            return \Redirect::back();

            }catch (\Throwable $e) {

                \Log::error('forgot_password mail failed: '.$e->getMessage());
                // Password was NOT changed. Surface a real error to the admin.
                \Session::flash('error_flash_message', trans('words.something_went_wrong'));
                return \Redirect::back();
            }     
  
        }
         
        
        
    }
    	
}
