<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Services\StudentNotifier;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
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

        ApplicationActivity::query()->create([
            'application_id' => $record->id,
            'user_id' => auth()->id(),
            'type' => 'status_change',
            'from_status' => $this->previousStatus,
            'to_status' => $newStatus,
            'body' => 'Status updated in admin',
        ]);

        $user = $record->student?->user;
        if ($user) {
            app(StudentNotifier::class)->notify(
                $user,
                'Application status updated',
                "Application {$record->application_no} is now {$newStatus}.",
                'info',
                '/student/applications',
            );
        }
    }
}
