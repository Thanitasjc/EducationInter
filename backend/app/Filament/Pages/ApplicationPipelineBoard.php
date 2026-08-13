<?php

namespace App\Filament\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Concerns\ScopesToConsultant;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Services\ApplicationPipelineService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ApplicationPipelineBoard extends Page
{
    use ScopesToConsultant;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string $view = 'filament.pages.application-pipeline-board';

    protected static ?string $navigationGroup = 'การรับสมัคร';

    protected static ?string $navigationLabel = 'App Pipeline';

    protected static ?string $title = 'Application Pipeline';

    protected static ?int $navigationSort = 0;

    /** @var list<ApplicationStatus> */
    public array $columnStatuses = [
        ApplicationStatus::Consultation,
        ApplicationStatus::DocumentRequired,
        ApplicationStatus::ReadyToApply,
        ApplicationStatus::Submitted,
        ApplicationStatus::ConditionalOffer,
        ApplicationStatus::UnconditionalOffer,
        ApplicationStatus::Visa,
    ];

    public function getColumns(): Collection
    {
        $query = Application::query()
            ->with(['student.user:id,name', 'consultant:id,name'])
            ->whereIn('status', array_map(fn (ApplicationStatus $s) => $s->value, $this->columnStatuses))
            ->latest('updated_at');

        if (static::shouldScopeToConsultant()) {
            $query->where('consultant_id', static::consultantUserId());
        }

        $apps = $query->limit(300)->get()->groupBy(
            fn (Application $app) => $app->status instanceof ApplicationStatus
                ? $app->status->value
                : (string) $app->status
        );

        return collect($this->columnStatuses)->mapWithKeys(
            fn (ApplicationStatus $status) => [
                $status->value => $apps->get($status->value, collect()),
            ]
        );
    }

    public function advanceApplication(int $applicationId, string $status, ApplicationPipelineService $pipeline): void
    {
        $application = Application::query()->findOrFail($applicationId);

        if (static::shouldScopeToConsultant() && $application->consultant_id !== static::consultantUserId()) {
            Notification::make()->title('Not allowed')->danger()->send();

            return;
        }

        $pipeline->changeStatus($application, $status, auth()->user());

        Notification::make()->title('Application advanced')->success()->send();
    }

    public function applicationEditUrl(Application $application): string
    {
        return ApplicationResource::getUrl('edit', ['record' => $application]);
    }

    public function nextStatus(string $current): ?string
    {
        $values = array_map(fn (ApplicationStatus $s) => $s->value, $this->columnStatuses);
        $index = array_search($current, $values, true);

        if ($index === false || $index >= count($values) - 1) {
            return null;
        }

        return $values[$index + 1];
    }
}
