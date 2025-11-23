<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    protected $fillable = [
        'user_id',
        'total_share',
        'total_average_share',
        'dividend_amount',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
