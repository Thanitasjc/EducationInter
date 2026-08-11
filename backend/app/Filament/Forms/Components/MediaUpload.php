<?php

namespace App\Filament\Forms\Components;

use App\Support\Media;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->disk(Media::diskName())
            ->visibility('public')
            ->image()
            ->downloadable()
            ->openable()
            ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                try {
                    if (! $file->exists()) {
                        return null;
                    }
                } catch (\League\Flysystem\UnableToCheckFileExistence $exception) {
                    return null;
                }

                $disk = Media::diskName();
                $directory = $component->getDirectory() ?? 'uploads';
                $name = $component->getUploadedFileNameForStorage($file);

                // Prefer storeAs — Supabase S3 often rejects ACL-based storePubliclyAs.
                $path = $file->storeAs($directory, $name, $disk);

                return Media::url($path) ?? Storage::disk($disk)->url($path);
            })
            ->deleteUploadedFileUsing(function (BaseFileUpload $component, string $file): void {
                if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
                    $relative = Media::relativePathFromUrl($file);
                    if ($relative) {
                        Media::disk()->delete($relative);
                    }

                    return;
                }

                Media::disk()->delete($file);
            })
            ->getUploadedFileUsing(function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                $url = Media::url($file);
                if (! $url) {
                    return null;
                }

                $name = ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames)
                    ?? basename(parse_url($url, PHP_URL_PATH) ?: $file);

                return [
                    'name' => is_string($name) ? $name : basename($file),
                    'size' => 0,
                    'type' => null,
                    'url' => $url,
                ];
            });
    }
}
