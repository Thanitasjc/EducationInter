<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopesToConsultant
{
    public static function shouldScopeToConsultant(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('consultant')
            && ! $user->hasAnyRole(['super_admin', 'admin', 'admission_officer']);
    }

    public static function consultantUserId(): ?int
    {
        return Auth::id();
    }

    public static function scopeAssignedQuery(Builder $query, string $column): Builder
    {
        if (! static::shouldScopeToConsultant()) {
            return $query;
        }

        return $query->where($column, static::consultantUserId());
    }
}
