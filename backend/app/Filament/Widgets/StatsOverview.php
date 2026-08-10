<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Lead;
use App\Models\Student;
use App\Models\University;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Leads', Lead::query()->count())
                ->description('All inbound leads')
                ->color('primary'),
            Stat::make('Applications', Application::query()->count())
                ->description('Admission pipeline')
                ->color('success'),
            Stat::make('Students', Student::query()->count())
                ->description('Registered students')
                ->color('info'),
            Stat::make('Universities', University::query()->count())
                ->description('Catalog entries')
                ->color('warning'),
        ];
    }
}
