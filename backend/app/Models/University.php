<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class University extends Model
{
    protected $fillable = [
        'country_id',
        'city_id',
        'slug',
        'name_th',
        'name_en',
        'type',
        'logo_path',
        'cover_path',
        'ranking_qs',
        'ranking_the',
        'tuition_min',
        'tuition_max',
        'currency',
        'about_th',
        'about_en',
        'entry_requirements',
        'accommodation_info',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'entry_requirements' => 'array',
            'accommodation_info' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'tuition_min' => 'decimal:2',
            'tuition_max' => 'decimal:2',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarship::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
}
