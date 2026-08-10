<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Course extends Model
{
    protected $fillable = [
        'university_id',
        'course_category_id',
        'slug',
        'name_th',
        'name_en',
        'degree_level',
        'duration_months',
        'tuition',
        'currency',
        'intakes',
        'entry_requirements',
        'english_requirements',
        'summary_th',
        'summary_en',
        'cover_path',
        'is_popular',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'intakes' => 'array',
            'entry_requirements' => 'array',
            'english_requirements' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'tuition' => 'decimal:2',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
}
