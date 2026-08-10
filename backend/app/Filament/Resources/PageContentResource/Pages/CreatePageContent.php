<?php

namespace App\Filament\Resources\PageContentResource\Pages;

use App\Filament\Resources\PageContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePageContent extends CreateRecord
{
    protected static string $resource = PageContentResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $value = $data['value'] ?? [];
        if (! is_array($value)) {
            return $data;
        }

        $uploaded = $value['hero_image'] ?? null;
        $url = $value['hero_image_url'] ?? null;

        if (blank($uploaded) && filled($url)) {
            $value['hero_image'] = $url;
        }

        unset($value['hero_image_url']);

        if (isset($value['slides']) && is_array($value['slides'])) {
            $value['slides'] = array_values(array_map(static function ($slide) {
                if (! is_array($slide)) {
                    return $slide;
                }

                $uploaded = $slide['image'] ?? null;
                $url = $slide['image_url'] ?? null;

                if (blank($uploaded) && filled($url)) {
                    $slide['image'] = $url;
                }

                unset($slide['image_url']);

                return $slide;
            }, $value['slides']));
        }

        $data['value'] = $value;

        return $data;
    }
}
