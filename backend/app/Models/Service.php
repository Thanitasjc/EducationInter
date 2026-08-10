<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'title_th',
        'title_en',
        'summary_th',
        'summary_en',
        'content_th',
        'content_en',
        'icon_path',
        'image_path',
        'cta_label_th',
        'cta_label_en',
        'cta_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
