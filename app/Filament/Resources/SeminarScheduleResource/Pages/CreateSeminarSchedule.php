<?php

namespace App\Filament\Resources\SeminarScheduleResource\Pages;

use App\Filament\Resources\SeminarScheduleResource;
use App\Mail\SeminarCreatedNotification;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class CreateSeminarSchedule extends CreateRecord
{
    protected static string $resource = SeminarScheduleResource::class;
}
