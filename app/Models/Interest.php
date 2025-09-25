<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $table = 'interest';
    protected $fillable = [
        'interest_rate'
    ];

    public static function latestRate(): ?float
    {
        return self::orderBy('created_at', 'desc')->value('interest_rate');
    }
}
