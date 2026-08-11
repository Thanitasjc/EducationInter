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
            Stat::make('ลีด', Lead::query()->count())
                ->description('ลีดทั้งหมดที่เข้ามา')
                ->color('primary'),
            Stat::make('ใบสมัคร', Application::query()->count())
                ->description('ท่อการรับสมัคร')
                ->color('success'),
            Stat::make('นักเรียน', Student::query()->count())
                ->description('นักเรียนที่ลงทะเบียน')
                ->color('info')
                ->url(\App\Filament\Resources\StudentResource::getUrl()),
            Stat::make('มหาวิทยาลัย', University::query()->count())
                ->description('รายการในแคตตาล็อก')
                ->color('warning'),
        ];
    }
}
