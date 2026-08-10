<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Scholarship extends Model
{
    protected $fillable = [
        'country_id',
        'university_id',
        'slug',
        'title_th',
        'title_en',
        'amount_label_th',
        'amount_label_en',
        'cover_path',
        'logo_path',
        'deadline',
        'eligibility',
        'requirements',
        'how_to_apply_th',
        'how_to_apply_en',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'eligibility' => 'array',
            'requirements' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
}
