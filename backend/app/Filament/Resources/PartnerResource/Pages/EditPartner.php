<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $logo = $data['logo_path'] ?? null;
        if (is_string($logo) && str_starts_with($logo, 'http')) {
            $data['logo_url'] = $logo;
            $data['logo_path'] = null;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['logo_path'] ?? null) && filled($data['logo_url'] ?? null)) {
            $data['logo_path'] = $data['logo_url'];
        }

        unset($data['logo_url']);

        return $data;
    }
}
