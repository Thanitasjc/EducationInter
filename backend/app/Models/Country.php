<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Country extends Model
{
    protected $fillable = [
        'slug',
        'name_th',
        'name_en',
        'code',
        'flag_path',
        'cover_path',
        'summary_th',
        'summary_en',
        'content_th',
        'content_en',
        'tuition_info',
        'living_cost_info',
        'visa_info',
        'intakes',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tuition_info' => 'array',
            'living_cost_info' => 'array',
            'visa_info' => 'array',
            'intakes' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
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
