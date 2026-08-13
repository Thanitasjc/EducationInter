<?php

namespace App\Filament\Resources\StudentNotificationResource\Pages;

use App\Filament\Resources\StudentNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentNotifications extends ListRecords
{
    protected static string $resource = StudentNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
