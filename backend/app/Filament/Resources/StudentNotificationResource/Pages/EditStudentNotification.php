<?php

namespace App\Filament\Resources\StudentNotificationResource\Pages;

use App\Filament\Resources\StudentNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentNotification extends EditRecord
{
    protected static string $resource = StudentNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
