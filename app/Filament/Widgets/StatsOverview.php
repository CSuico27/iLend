<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\UserProfile;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $daysInMonth = now()->daysInMonth; 

        $generateWaveChart = function (int $totalCount, int $days = 30): array {
        if ($totalCount === 0) {
            return array_fill(0, $days, 0); 
        }

        $minAmplitude = 3; 

        return collect(range(1, $days))->map(function ($day) use ($totalCount, $days, $minAmplitude) {
            
            $wave = sin($day / $days * 2 * pi());

            $normalized = 0.5 + 0.5 * $wave;

            $scaled = $normalized * max($totalCount, $minAmplitude);

            return round($scaled, 2);
        })->toArray();
    };

        $pendingCount = UserProfile::where('is_applied_for_membership', 1)
            ->where('status', 'Pending')
            ->count();

        $approvedCount = UserProfile::where('is_applied_for_membership', 1)
            ->where('status', 'Approved')
            ->count();

        $loanCount = Loan::where('status', 'Approved')
            ->where('is_finished', 0)
            ->count();

        return [
            Stat::make('Membership Application', $pendingCount)
                ->description('Pending members')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('warning')
                ->chart($generateWaveChart($pendingCount, $daysInMonth))
                ->url(route('filament.admin.resources.membership-managements.index')),

            Stat::make('Employee-Members', $approvedCount)
                ->description('Approved members')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success')
                ->chart($generateWaveChart($approvedCount, $daysInMonth))
                ->url(route('filament.admin.resources.member-lists.index')),

            Stat::make('Active Loans', $loanCount)
                ->description('Approved and ongoing loans')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('info')
                ->chart($generateWaveChart($loanCount, $daysInMonth))
                ->url(route('filament.admin.resources.loans-managements.index', [
                    'activeTab' => 'Approved',
                ])),
        ];
    }
}
