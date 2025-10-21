<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    
    protected $fillable = ['message', 'is_read', 'loan_id', 'ledger_id'];
}
