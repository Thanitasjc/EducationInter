<?php

namespace App\Filament\Resources\ConsultantResource\Pages;

use App\Filament\Resources\ConsultantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultant extends EditRecord
{
    protected static string $resource = ConsultantResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;
        $data['user_name'] = $user?->name;
        $data['user_email'] = $user?->email;
        $data['user_phone'] = $user?->phone;
        $data['user_is_active'] = $user?->is_active ?? true;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return collect($data)
            ->except(['user_name', 'user_email', 'user_phone', 'user_password', 'user_is_active'])
            ->all();
    }

    protected function afterSave(): void
    {
        ConsultantResource::syncConsultantUser($this->record, $this->form->getState(), false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function (): void {
                    $user = $this->record->user;
                    $this->record->delete();
                    $user?->delete();
                }),
        ];
    }
}
