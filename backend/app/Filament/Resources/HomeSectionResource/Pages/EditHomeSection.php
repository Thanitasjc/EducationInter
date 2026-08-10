<?php

namespace App\Filament\Resources\HomeSectionResource\Pages;

use App\Filament\Resources\HomeSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeSection extends EditRecord
{
    protected static string $resource = HomeSectionResource::class;

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
        $items = $data['items'] ?? [];
        if (! is_array($items)) {
            return $data;
        }

        $data['items'] = array_map(static function ($item) {
            if (! is_array($item)) {
                return $item;
            }

            $cover = $item['cover_path'] ?? null;
            if (is_string($cover) && str_starts_with($cover, 'http')) {
                $item['cover_url'] = $cover;
                $item['cover_path'] = null;
            }

            return $item;
        }, $items);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
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
