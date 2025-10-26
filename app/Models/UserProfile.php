<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'phone',
        'birthdate',
        'gender',
        'marital_status',
        'avatar',
        'biodata',
        'brgy_clearance',
        'valid_id',
        'tin_number',
        'approved_at',
        'is_applied_for_membership',
        'status',
        'region',
        'province',
        'municipality',
        'barangay'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
