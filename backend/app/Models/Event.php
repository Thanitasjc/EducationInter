<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'slug',
        'title_th',
        'title_en',
        'summary_th',
        'summary_en',
        'content_th',
        'content_en',
        'cover_path',
        'starts_at',
        'ends_at',
        'location',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
