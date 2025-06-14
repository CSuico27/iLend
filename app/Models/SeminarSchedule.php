<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarSchedule extends Model
{
    protected $fillable = [
        'title',
        'user_ids',
        'seminar_date',
        'start_time',
        'end_time',
        'status',
        'details',
    ];
    protected $casts = [
    'user_ids' => 'array',
    ];
    public function getAssignedUsersNamesAttribute(): array
    {
        if (empty($this->user_ids)) {
            return [];
        }

        return User::whereIn('id', $this->user_ids)->pluck('name')->toArray();
    }

}
