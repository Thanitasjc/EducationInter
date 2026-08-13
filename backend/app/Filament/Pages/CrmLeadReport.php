<?php

namespace App\Filament\Pages;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CrmLeadReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.crm-lead-report';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'รายงานลีด';

    protected static ?string $title = 'Lead source → conversion';

    protected static ?int $navigationSort = 5;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notifyFollowUps')
                ->label('Notify due follow-ups')
                ->icon('heroicon-o-bell-alert')
                ->requiresConfirmation()
                ->action(function (): void {
                    Artisan::call('crm:notify-follow-ups');
                    Notification::make()
                        ->title('Follow-up notifications sent')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getRows(): Collection
    {
        $convertedStatuses = [
            LeadStatus::Application->value,
            LeadStatus::Submitted->value,
            LeadStatus::Offer->value,
            LeadStatus::Visa->value,
            LeadStatus::Success->value,
        ];

        $successStatuses = [LeadStatus::Success->value];

        return Lead::query()
            ->select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($convertedStatuses, $successStatuses) {
                $source = $row->source ?: 'unknown';
                $total = (int) $row->total;
                $converted = Lead::query()
                    ->where('source', $row->source)
                    ->whereIn('status', $convertedStatuses)
                    ->count();
                $success = Lead::query()
                    ->where('source', $row->source)
                    ->whereIn('status', $successStatuses)
                    ->count();
                $withApp = Lead::query()
                    ->where('source', $row->source)
                    ->whereHas('application')
                    ->count();

                return [
                    'source' => $source,
                    'total' => $total,
                    'with_application' => $withApp,
                    'pipeline_converted' => $converted,
                    'success' => $success,
                    'convert_rate' => $total > 0 ? round(($withApp / $total) * 100, 1) : 0,
                    'success_rate' => $total > 0 ? round(($success / $total) * 100, 1) : 0,
                ];
            });
    }

    public function getDueCount(): int
    {
        return Lead::query()
            ->whereNotIn('status', [LeadStatus::Success->value, LeadStatus::Lost->value])
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now()->endOfDay())
            ->count();
    }
}
