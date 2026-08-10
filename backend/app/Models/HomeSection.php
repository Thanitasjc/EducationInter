<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSection extends Model
{
    protected $fillable = [
        'key',
        'layout',
        'title_th',
        'title_en',
        'subtitle_th',
        'subtitle_en',
        'cover_path',
        'items',
        'cta_label_th',
        'cta_label_en',
        'cta_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function coverUrl(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        if (str_starts_with($this->cover_path, 'http://') || str_starts_with($this->cover_path, 'https://')) {
            return $this->cover_path;
        }

        return asset('storage/'.$this->cover_path);
    }

    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): ?string
    {
        return $this->coverUrl();
    }
}
