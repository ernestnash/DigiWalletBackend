<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_number',
        'account_type',
        'account_balance',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'account_number', 'id');
    }
    public function transactions() {
        return $this->hasMany(Transaction::class, 'account_number', 'id');
    }
    public function cheque() {
        return $this->hasMany(Cheque::class, 'account_number', 'id');
    }

}
