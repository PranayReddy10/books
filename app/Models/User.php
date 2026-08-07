<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'registered_via',
        'last_login_at',
        'last_login_via',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getUserInfo($id) 
    { 
        return User::find($id);
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

    /**
     * Human-readable description of how this account was created.
     * e.g. "Google (App)", "App form", "Website form".
     */
    public function getSourceLabelAttribute()
    {
        $via = $this->registered_via; // 'app' | 'website' | null
        $social = $this->social_login_type; // 'google' | 'facebook' | null

        if ($social) {
            $name = ucfirst($social);
            return $via ? $name.' ('.ucfirst($via).')' : $name;
        }

        if ($via === 'app') {
            return 'App form';
        }
        if ($via === 'website') {
            return 'Website form';
        }

        return 'Unknown';
    }

    /**
     * Record a successful login. $via = 'app' | 'website'.
     */
    public function touchLogin($via)
    {
        $this->last_login_at  = now();
        $this->last_login_via = $via;
        $this->save();
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
        $url=url('admin/password/reset/'.$this->token);
 

        return (new MailMessage)
            ->subject('Reset Password')
            ->from(getcong('app_email'), getcong('app_name'))
            /*->line('We are sending this email because we recieved a forgot password request.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required. Please contact us if you did not submit this request.');*/
            ->view('emails.password',['url'=>$url]);
    }
}
