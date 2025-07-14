<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditScore extends Model
{
    protected $fillable = ['user_id', 'score', 'tier', 'remarks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
