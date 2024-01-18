<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_number',
        'transaction_type',
        'amount',
        'origin_account',
        'destination_account',
        'description',
        'reference',
        'method',
        'fee',
        'running_balance',
        'status',
    ];

    public function account() {
        return $this->belongsTo(Account::class, 'account_number', 'account_number');
    }
    public function user() {
        return $this->belongsTo(User::class, 'account_number', 'id');
    }
}
