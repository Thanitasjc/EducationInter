<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\LeadResource;
use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationPipelineService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('openLead')
                ->label('Open lead')
                ->icon('heroicon-o-inbox-arrow-down')
                ->url(fn (): ?string => $this->record->lead_id
                    ? LeadResource::getUrl('edit', ['record' => $this->record->lead_id])
                    : null)
                ->visible(fn (): bool => filled($this->record->lead_id)),
            Actions\Action::make('advance')
                ->label('Advance status')
                ->icon('heroicon-o-arrow-right-circle')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(collect(ApplicationStatus::cases())->mapWithKeys(
                            fn (ApplicationStatus $status) => [$status->value => strtoupper(str_replace('_', ' ', $status->value))]
                        ))
                        ->required(),
                    Forms\Components\TextInput::make('next_action'),
                    Forms\Components\Textarea::make('note')->rows(2),
                ])
                ->action(function (array $data, ApplicationPipelineService $pipeline): void {
                    $pipeline->changeStatus(
                        $this->record,
                        $data['status'],
                        auth()->user(),
                        $data['note'] ?? null,
                        $data['next_action'] ?? null,
                    );
                    $this->refreshFormData(['status', 'next_action', 'submitted_at']);
                    Notification::make()->title('Application status updated')->success()->send();
                }),
            Actions\Action::make('assign')
                ->label('Assign')
                ->icon('heroicon-o-user-plus')
                ->form([
                    Forms\Components\Select::make('consultant_id')
                        ->options(fn () => User::query()
                            ->role(['consultant', 'admission_officer', 'admin', 'super_admin'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, ApplicationPipelineService $pipeline): void {
                    $consultant = User::query()->findOrFail($data['consultant_id']);
                    $pipeline->assign($this->record, $consultant, auth()->user());
                    $this->refreshFormData(['consultant_id']);
                    Notification::make()->title('Application assigned')->success()->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousStatus = $this->record->getOriginal('status')?->value
            ?? (string) $this->record->getOriginal('status');
    }

    protected function afterSave(): void
    {
        /** @var Application $record */
        $record = $this->record;
        $newStatus = $record->status instanceof \BackedEnum
            ? $record->status->value
            : (string) $record->status;

        if ($this->previousStatus === null || $this->previousStatus === $newStatus) {
            return;
        }

        // Form already persisted the new status; rewind in-memory so the pipeline can log/sync.
        $record->status = ApplicationStatus::from($this->previousStatus);

        app(ApplicationPipelineService::class)->changeStatus(
            $record,
            $newStatus,
            auth()->user(),
            'Status updated in admin form',
            $record->next_action,
        );
    }
}
