<?php

namespace App\Filament\Pages;

use App\Enums\LeadStatus;
use App\Filament\Concerns\ScopesToConsultant;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use App\Services\LeadPipelineService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class LeadPipelineBoard extends Page
{
    use ScopesToConsultant;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static string $view = 'filament.pages.lead-pipeline-board';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'Pipeline';

    protected static ?string $title = 'Lead Pipeline';

    protected static ?int $navigationSort = 0;

    /** @var list<LeadStatus> */
    public array $columnStatuses = [
        LeadStatus::New,
        LeadStatus::Contacted,
        LeadStatus::Consultation,
        LeadStatus::Interested,
        LeadStatus::Document,
        LeadStatus::Application,
    ];

    public function getColumns(): Collection
    {
        $query = Lead::query()
            ->with(['assignee:id,name', 'application:id,lead_id,application_no'])
            ->whereIn('status', array_map(fn (LeadStatus $s) => $s->value, $this->columnStatuses))
            ->latest('updated_at');

        if (static::shouldScopeToConsultant()) {
            $query->where('assigned_to', static::consultantUserId());
        }

        $leads = $query->limit(300)->get()->groupBy(
            fn (Lead $lead) => $lead->status instanceof LeadStatus
                ? $lead->status->value
                : (string) $lead->status
        );

        return collect($this->columnStatuses)->mapWithKeys(
            fn (LeadStatus $status) => [
                $status->value => $leads->get($status->value, collect()),
            ]
        );
    }

    public function advanceLead(int $leadId, string $status, LeadPipelineService $pipeline): void
    {
        $lead = Lead::query()->findOrFail($leadId);

        if (static::shouldScopeToConsultant() && $lead->assigned_to !== static::consultantUserId()) {
            Notification::make()->title('Not allowed')->danger()->send();

            return;
        }

        $pipeline->changeStatus($lead, $status, auth()->user());

        Notification::make()->title('Lead advanced')->success()->send();
    }

    public function convertLead(int $leadId, LeadPipelineService $pipeline)
    {
        $lead = Lead::query()->findOrFail($leadId);

        if (static::shouldScopeToConsultant() && $lead->assigned_to !== static::consultantUserId()) {
            Notification::make()->title('Not allowed')->danger()->send();

            return null;
        }

        try {
            $application = $pipeline->convertToApplication($lead, auth()->user());

            Notification::make()
                ->title('Application created')
                ->body($application->application_no)
                ->success()
                ->send();

            return redirect(ApplicationResource::getUrl('edit', ['record' => $application]));
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Convert failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    public function leadEditUrl(Lead $lead): string
    {
        return LeadResource::getUrl('edit', ['record' => $lead]);
    }

    public function nextStatus(string $current): ?string
    {
        $values = array_map(fn (LeadStatus $s) => $s->value, $this->columnStatuses);
        $index = array_search($current, $values, true);

        if ($index === false || $index >= count($values) - 1) {
            return null;
        }

        return $values[$index + 1];
    }
}
