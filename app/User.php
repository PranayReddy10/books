<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['usertype','role_id','name','username','email', 'password','user_image','mobile','remember_token','phone','rollnumber','university','department_id','college','gender'];

    public function department()
    {
        return $this->belongsTo('App\Department', 'department_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getUserInfo($id,$field_name=null) 
    { 
         
        $info = User::where('id',$id)->first();
        
        if($info)
		{   
            if($field_name!='')
            {
                return  $info->$field_name;
            }
            else
            {
                
                return  $info;

            }

		}
		else
		{
			return  '';
		}

    }

    public static function getUserFullname($id) 
    { 
        $userinfo=User::find($id);

        if($userinfo)
        {
            return $userinfo->name;
        }
        else
        {
            return  '';
        }
        
    }

    public function sendPasswordResetNotification($token)
    {

        $this->notify(new CustomPassword($token));
    }
}

class CustomPassword extends ResetPassword
{
    public function toMail($notifiable)
    {   
        $url=url('password/reset/'.$this->token);
 

        return (new MailMessage)
            ->subject('Reset Password')
            ->from(getcong('app_email'), getcong('app_name'))
            /*->line('We are sending this email because we recieved a forgot password request.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required. Please contact us if you did not submit this request.');*/
            ->view('emails.password',['url'=>$url]);
    }
}
