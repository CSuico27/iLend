<?php

namespace App\Filament\Resources\SeminarScheduleResource\Pages;

use App\Filament\Resources\SeminarScheduleResource;
use App\Mail\SeminarCreatedNotification;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;

class ListSeminarSchedules extends ListRecords
{
    protected static string $resource = SeminarScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record, $data) {
                    $assignedUserIds = $data['user_ids'] ?? [];
                    $assignedUsers = User::whereIn('id', $assignedUserIds)->get();
                    $emailBody = "We look forward to your participation!";

                    foreach ($assignedUsers as $user) {
                        Mail::to($user->email)->send(new SeminarCreatedNotification($record, $emailBody, $user->name));
                }
            }),
        ];
    }
}
