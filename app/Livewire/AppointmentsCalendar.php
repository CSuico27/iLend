<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\SeminarSchedule;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Omnia\LivewireCalendar\LivewireCalendar;
use Livewire\Attributes\On;

class AppointmentsCalendar extends LivewireCalendar
{
    public ?string $title;
    public ?string $description;
    public ?string $date;
    public ?string $startTime;
    public ?string $endTime;
    public bool $showModal = false;
    public ?Carbon $currentMonth = null;
    public array $userIds = [];
    public array $userNames = [];
    public array $userPicture = [];

    public function events(): Collection
    {
        if (!$this->currentMonth) {
            $this->currentMonth = Carbon::now()->startOfMonth();
            $this->setMonthRange($this->currentMonth);
        }
        return SeminarSchedule::query()
            ->whereDate('seminar_date', '>=', $this->gridStartsAt)
            ->whereDate('seminar_date', '<=', $this->gridEndsAt)
            ->get()
            ->map(function ($event) {
                return [
                    'id'          => $event->id,
                    'title'       => $event->title,
                    'description' => $event->details,
                    'date'        => Carbon::parse($event->seminar_date),
                    'start_time'  => Carbon::parse($event->start_time)->format('g:i A'),
                    'end_time'    => Carbon::parse($event->end_time)->format('g:i A'),
                    'user_ids'    => $event->user_ids,
                ];
            });

    }

    public function onEventClick($eventId)
    {
        $event = collect($this->events())->firstWhere('id', $eventId);

        if (!$event) return;

        $this->title = $event['title'] ?? 'No Title';
        $this->description = $event['description'] ?? 'No Description';
        $this->date = Carbon::parse($event['date'])->format('F j, Y');
        $this->startTime = $event['start_time'] ?? null;
        $this->endTime = $event['end_time'] ?? null;
        
        $seminar = SeminarSchedule::find($eventId);
        $userIds = $seminar->user_ids ?? [];

        $this->userNames = User::whereIn('id', $userIds)
    ->with('info')
    ->get()
    ->map(function ($user) {
        return [
            'email' => $user->email,
            'name' => $user->name,
            'picture' =>$user->info?->picture,
        ];
    })->toArray();



        // $this->userPicture = UserProfile::with('info')
        //     ->whereIn('user_id', $user)
        //     ->pluck('picture')
        //     ->toArray(); 

        $this->showModal = true;
    }


    public function goToPreviousMonth()
    {
        if (!$this->currentMonth) {
            $this->currentMonth = Carbon::now()->startOfMonth();
        }
        $this->currentMonth = $this->currentMonth->copy()->subMonth();
        $this->setMonthRange($this->currentMonth);
    }

    public function goToNextMonth()
    {
        if (!$this->currentMonth) {
            $this->currentMonth = Carbon::now()->startOfMonth();
        }
        $this->currentMonth = $this->currentMonth->copy()->addMonth();
        $this->setMonthRange($this->currentMonth);
    }

    public function goToCurrentMonth()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
        $this->setMonthRange($this->currentMonth);
    }

    protected function setMonthRange(Carbon $monthStart)
    {
        $this->gridStartsAt = $monthStart->copy()->startOfWeek();
        $this->gridEndsAt = $this->gridStartsAt->copy()->addDays(34);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'description', 'date', 'startTime', 'endTime']);
    }

    #[On('close-modal')]
    public function handleCloseModal($modalId = null)
    {
        if ($modalId === 'simpleModal' || $modalId === null) {
            $this->closeModal();
        }
    }
}
