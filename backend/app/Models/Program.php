<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'slug',
        'title_th',
        'title_en',
        'summary_th',
        'summary_en',
        'content_th',
        'content_en',
        'age_min',
        'age_max',
        'duration_label_th',
        'duration_label_en',
        'language',
        'destinations',
        'cover_path',
        'cta_label_th',
        'cta_label_en',
        'cta_url',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'destinations' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['cover_url', 'age_label'];

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

    public function getCoverUrlAttribute(): ?string
    {
        return $this->coverUrl();
    }

    public function getAgeLabelAttribute(): ?string
    {
        if ($this->age_min === null && $this->age_max === null) {
            return null;
        }

        if ($this->age_min !== null && $this->age_max !== null) {
            if ($this->age_max >= 99) {
                return $this->age_min.'+';
            }

            return $this->age_min.'–'.$this->age_max;
        }

        if ($this->age_min !== null) {
            return $this->age_min.'+';
        }

        return '≤'.$this->age_max;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForAgeGroup(Builder $query, ?string $group): Builder
    {
        if (! $group || $group === 'all') {
            return $query;
        }

        [$min, $max] = match ($group) {
            '12-16' => [12, 16],
            '16-18' => [16, 18],
            '18-25' => [18, 25],
            '25-plus' => [25, 99],
            '50-plus' => [50, 99],
            default => [null, null],
        };

        if ($min === null) {
            return $query;
        }

        // Overlap: program range intersects selected range
        return $query
            ->where(function (Builder $q) use ($min, $max) {
                $q->whereNull('age_min')->whereNull('age_max')
                    ->orWhere(function (Builder $inner) use ($min, $max) {
                        $inner->where(function (Builder $a) use ($max) {
                            $a->whereNull('age_min')->orWhere('age_min', '<=', $max);
                        })->where(function (Builder $b) use ($min) {
                            $b->whereNull('age_max')->orWhere('age_max', '>=', $min);
                        });
                    });
            });
    }
}
