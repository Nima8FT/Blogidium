<?php

namespace Modules\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Auth\Database\Factories\LinkedSocialAccountFactory;

class LinkedSocialAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'provider_id',
        'provider_name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // protected static function newFactory(): LinkedSocialAccountFactory
    // {
    //     // return LinkedSocialAccountFactory::new();
    // }
}
