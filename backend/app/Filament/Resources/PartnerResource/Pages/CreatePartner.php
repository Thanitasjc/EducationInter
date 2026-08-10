<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['logo_path'] ?? null) && filled($data['logo_url'] ?? null)) {
            $data['logo_path'] = $data['logo_url'];
        }

        unset($data['logo_url']);

        return $data;
    }
}
