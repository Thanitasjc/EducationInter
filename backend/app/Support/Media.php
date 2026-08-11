<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    public static function diskName(): string
    {
        return (string) config('filesystems.media_disk', config('filesystems.default', 'public'));
    }

    public static function disk()
    {
        return Storage::disk(static::diskName());
    }

    /**
     * Turn a stored path or URL into a durable public URL.
     */
    public static function url(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return static::disk()->url(ltrim($path, '/'));
    }

    /**
     * Best-effort relative key from a public URL (for deletes).
     */
    public static function relativePathFromUrl(string $url): ?string
    {
        $base = rtrim((string) config('filesystems.disks.'.static::diskName().'.url'), '/');
        if ($base !== '' && str_starts_with($url, $base.'/')) {
            return ltrim(substr($url, strlen($base) + 1), '/');
        }

        $publicBase = rtrim((string) env('MEDIA_PUBLIC_BASE_URL', ''), '/');
        if ($publicBase !== '' && str_starts_with($url, $publicBase.'/')) {
            return ltrim(substr($url, strlen($publicBase) + 1), '/');
        }

        return null;
    }
}
