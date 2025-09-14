<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Auth\Database\Factories\UserTwoFAFactory;

class UserTwoFA extends Model
{
    use HasFactory;

    protected $table = 'user_twofa';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'secret_code', 'enabled', 'temp_token'];

    // protected static function newFactory(): UserTwoFAFactory
    // {
    //     // return UserTwoFAFactory::new();
    // }
}
