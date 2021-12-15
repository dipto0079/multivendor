<?php

namespace App;

use App\Http\Controllers\Enum\UserTypeEnum;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function generateStrongPassword($length)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $special_char = '!@#$%^&*()';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        $special_charLength = strlen($special_char) - 1; //put the length -1 in cache
        for ($i = 0; $i <$length; $i++) {
            $n = rand(0, $alphaLength);
            $s = rand(0, $special_charLength);
            $pass[] = $alphabet[$n].$special_char[$s];
        }
        $rand_password = implode($pass); //turn the array into a string

        return $rand_password;
    }

    public function getSeller(){
        return $this->hasOne(Model\Seller::class,'user_id');
    }

    public function getBuyer(){
        return $this->hasOne(Model\Buyer::class,'user_id');
    }

    public function getRole(){
        return $this->belongsTo(Model\Role::class,'admin_role_id');
    }

    public static function getUserName($user_id='')
    {
      $username = '';

      if(!empty($user_id)){
        $user = User::find($user_id);
        $username = $user->username;
      }

      return $username;
    }

}
