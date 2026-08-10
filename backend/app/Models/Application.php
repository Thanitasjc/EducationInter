<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'application_no',
        'student_id',
        'consultant_id',
        'country_id',
        'university_id',
        'course_id',
        'intake',
        'status',
        'next_action',
        'current_step',
        'personal_data',
        'education_data',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'personal_data' => 'array',
            'education_data' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ApplicationActivity::class);
    }
}
