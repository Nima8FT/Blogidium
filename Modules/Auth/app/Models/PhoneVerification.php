<?php

namespace Modules\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Auth\Database\Factories\PhoneVerificationFactory;

class PhoneVerification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'token', 'expires_at'];

    // protected static function newFactory(): PhoneVerificationFactory
    // {
    //     // return PhoneVerificationFactory::new();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
