<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'cheque_number',
        'account_number',
        'payee_name',
        'amount',
        'cheque_status',
        'date_issued',
        'date_cashed',
        'authorization_status',
        'stop_payment_flag',
        'issuing_branch',
        'memo',
    ];


    public function account() {
        return $this->belongsTo(Account::class, 'account_number', 'account_number');
    }
    public function user() {
        return $this->belongsTo(User::class, 'account_number', 'id');
    }
}
