<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'account_type',
        'account_balance',
        'status',
    ];

    
    public function account() {
        return $this->belongsTo(Account::class, 'account_number', 'account_number');
    }
    public function user() {
        return $this->belongsTo(User::class, 'account_number', 'id');
    }
}
