<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($user) {
    //         $user->id = static::generateIdNumber();
    //     });
    // }

    // protected static function generateIdNumber()
    // {
    //     // Generate a unique random account number of length 8
    // $IdNumber = mt_rand(1000000000, 9999999999);

    // // Ensure the generated account number is unique
    // while (static::where('account_number', $IdNumber)->exists()) {
    //     $IdNumber = mt_rand(1000000000, 9999999999);
    // }

    // return $IdNumber;
    // }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'pin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pin',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_number_verified_at' => 'datetime',
        'pin' => 'hashed',
    ];
}
