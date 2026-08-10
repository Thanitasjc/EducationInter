<?php

namespace App\Filament\Resources\HomeSectionResource\Pages;

use App\Filament\Resources\HomeSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeSection extends CreateRecord
{
    protected static string $resource = HomeSectionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = $data['items'] ?? [];
        if (! is_array($items)) {
            return $data;
        }

        $data['items'] = array_map(static function ($item) {
            if (! is_array($item)) {
                return $item;
            }

            $uploaded = $item['cover_path'] ?? null;
            $url = $item['cover_url'] ?? null;

            if (blank($uploaded) && filled($url)) {
                $item['cover_path'] = $url;
            }

            unset($item['cover_url']);

            return $item;
        }, $items);

        return $data;
    }
}
