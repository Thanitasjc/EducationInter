<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\Lead;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $dueFollowUps = 0;
            if (Schema::hasColumn('leads', 'next_follow_up_at')) {
                $dueFollowUps = Lead::query()
                    ->whereNotIn('status', ['success', 'lost'])
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<=', now()->endOfDay())
                    ->count();
            }

            return [
                Stat::make('ลีด', Lead::query()->count())
                    ->description('ลีดทั้งหมด')
                    ->color('primary')
                    ->url(\App\Filament\Resources\LeadResource::getUrl()),
                Stat::make('ติดตามวันนี้', $dueFollowUps)
                    ->description('Due / overdue follow-ups')
                    ->color($dueFollowUps > 0 ? 'danger' : 'gray')
                    ->url(\App\Filament\Resources\LeadResource::getUrl()),
                Stat::make('ใบสมัคร', Application::query()->count())
                    ->description('ท่อการรับสมัคร')
                    ->color('success')
                    ->url(\App\Filament\Resources\ApplicationResource::getUrl()),
                Stat::make('เอกสารรอตรวจ', Document::query()->where('status', 'pending')->count())
                    ->description('Pending review')
                    ->color('warning')
                    ->url(\App\Filament\Resources\DocumentResource::getUrl()),
                Stat::make('นัดหมายใกล้ถึง', Appointment::query()
                    ->where('status', 'scheduled')
                    ->where('starts_at', '>=', now())
                    ->count())
                    ->description('Scheduled upcoming')
                    ->color('info')
                    ->url(\App\Filament\Resources\AppointmentResource::getUrl()),
                Stat::make('นักเรียน', Student::query()->count())
                    ->description('ลงทะเบียนแล้ว')
                    ->color('gray')
                    ->url(\App\Filament\Resources\StudentResource::getUrl()),
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                Stat::make('Dashboard', 'OK')
                    ->description('Stats temporarily unavailable')
                    ->color('gray'),
            ];
        }
    }
}
