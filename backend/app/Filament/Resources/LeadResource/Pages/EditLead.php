<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Enums\LeadStatus;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\LeadResource;
use App\Models\User;
use App\Services\LeadPipelineService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    private ?string $originalStatus = null;

    private ?int $originalAssignee = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('convert')
                ->label('Convert to application')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn (): bool => filled($this->record->email) && ! $this->record->application()->exists())
                ->requiresConfirmation()
                ->action(function (LeadPipelineService $pipeline) {
                    try {
                        $application = $pipeline->convertToApplication($this->record, auth()->user());

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
                    }
                }),
            Actions\Action::make('schedule')
                ->label('Schedule appointment')
                ->icon('heroicon-o-calendar-days')
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->required()
                        ->default(fn () => 'Consultation: '.$this->record->name),
                    \Filament\Forms\Components\DateTimePicker::make('starts_at')
                        ->required()
                        ->seconds(false)
                        ->default(now()->addDay()->setTime(10, 0)),
                    \Filament\Forms\Components\DateTimePicker::make('ends_at')->seconds(false),
                    \Filament\Forms\Components\Select::make('type')
                        ->options([
                            'consultation' => 'Consultation',
                            'document_review' => 'Document review',
                            'interview' => 'Interview',
                            'follow_up' => 'Follow-up',
                            'other' => 'Other',
                        ])
                        ->default('consultation')
                        ->required(),
                    \Filament\Forms\Components\Select::make('consultant_id')
                        ->label('Consultant')
                        ->options(fn () => User::query()
                            ->role(['consultant', 'admission_officer', 'admin', 'super_admin'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->default(fn () => $this->record->assigned_to ?? auth()->id())
                        ->searchable()
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('notes')->rows(2),
                ])
                ->action(function (array $data, LeadPipelineService $pipeline): void {
                    $appointment = $pipeline->scheduleAppointment($this->record, $data, auth()->user());
                    Notification::make()
                        ->title('Appointment scheduled')
                        ->body($appointment->starts_at?->format('Y-m-d H:i'))
                        ->success()
                        ->send();
                }),
            Actions\Action::make('openApplication')
                ->label('Open application')
                ->icon('heroicon-o-document-text')
                ->url(fn (): ?string => $this->record->application
                    ? ApplicationResource::getUrl('edit', ['record' => $this->record->application])
                    : null)
                ->visible(fn (): bool => (bool) $this->record->application),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalStatus = $data['status'] instanceof LeadStatus
            ? $data['status']->value
            : ($data['status'] ?? null);
        $this->originalAssignee = $data['assigned_to'] ?? null;

        return $data;
    }

    protected function afterSave(): void
    {
        $lead = $this->record;
        $actor = auth()->user();

        $newStatus = $lead->status instanceof LeadStatus
            ? $lead->status->value
            : (string) $lead->status;

        if ($this->originalStatus && $this->originalStatus !== $newStatus) {
            $lead->activities()->create([
                'user_id' => $actor?->id,
                'type' => 'status_change',
                'from_status' => $this->originalStatus,
                'to_status' => $newStatus,
                'body' => 'Status updated from edit form',
            ]);
            $lead->forceFill(['last_contact_at' => now()])->save();
        }

        if (
            $this->originalAssignee !== $lead->assigned_to
            && $lead->assigned_to
            && $lead->assignee
        ) {
            $lead->activities()->create([
                'user_id' => $actor?->id,
                'type' => 'assignment',
                'body' => "Assigned to {$lead->assignee->name}",
            ]);
            $lead->forceFill(['last_contact_at' => now()])->save();
        }

        $this->originalStatus = $newStatus;
        $this->originalAssignee = $lead->assigned_to;
    }
}
