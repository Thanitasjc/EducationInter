<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use App\Services\StudentNotifier;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousStatus = (string) $this->record->getOriginal('status');
    }

    protected function afterSave(): void
    {
        /** @var Document $record */
        $record = $this->record;
        $user = $record->student?->user;

        if (! $user || $this->previousStatus === $record->status) {
            return;
        }

        if (! in_array($record->status, ['approved', 'rejected'], true)) {
            return;
        }

        app(StudentNotifier::class)->notify(
            $user,
            $record->status === 'approved' ? 'Document approved' : 'Document needs revision',
            $record->status === 'approved'
                ? "Your document \"{$record->name}\" was approved."
                : "Your document \"{$record->name}\" was rejected. ".($record->review_note ?: 'Please re-upload.'),
            $record->status === 'approved' ? 'success' : 'warning',
            '/student/documents',
        );
    }
}
