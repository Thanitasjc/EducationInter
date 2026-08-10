<?php

namespace App\Filament\Resources\PageContentResource\Pages;

use App\Filament\Resources\PageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageContent extends EditRecord
{
    protected static string $resource = PageContentResource::class;

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
    protected function mutateFormDataBeforeSave(array $data): array
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
            $value['slides'] = array_values(array_map(
                static fn ($slide) => self::normalizeSlideOnSave($slide),
                $value['slides']
            ));
        }

        $data['value'] = $value;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $value = $data['value'] ?? [];
        if (! is_array($value)) {
            return $data;
        }

        $hero = $value['hero_image'] ?? null;
        if (is_string($hero) && str_starts_with($hero, 'http')) {
            $value['hero_image_url'] = $hero;
            $value['hero_image'] = null;
        }

        if (isset($value['slides']) && is_array($value['slides'])) {
            $value['slides'] = array_map(
                static fn ($slide) => self::normalizeSlideOnFill($slide),
                $value['slides']
            );
        }

        $data['value'] = $value;

        return $data;
    }

    /**
     * @param  mixed  $slide
     * @return mixed
     */
    private static function normalizeSlideOnSave(mixed $slide): mixed
    {
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
    }

    /**
     * @param  mixed  $slide
     * @return mixed
     */
    private static function normalizeSlideOnFill(mixed $slide): mixed
    {
        if (! is_array($slide)) {
            return $slide;
        }

        $image = $slide['image'] ?? null;
        if (is_string($image) && str_starts_with($image, 'http')) {
            $slide['image_url'] = $image;
            $slide['image'] = null;
        }

        return $slide;
    }
}
