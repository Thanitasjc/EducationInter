<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'campaign',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'gclid',
        'fbclid',
        'country_id',
        'university_id',
        'course_id',
        'status',
        'assigned_to',
        'student_id',
        'message',
        'notes',
        'last_contact_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'last_contact_at' => 'datetime',
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
