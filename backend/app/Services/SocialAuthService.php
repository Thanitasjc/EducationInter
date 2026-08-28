<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthService
{
    public function resolveUser(
        string $provider,
        string $providerId,
        ?string $name = null,
        ?string $email = null,
        ?string $avatar = null,
    ): User {
        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if (! $user && $email) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => $name ?: 'Student',
                'email' => $email ?: "{$provider}_{$providerId}@users.local",
                'password' => Hash::make(Str::random(32)),
                'provider' => $provider,
                'provider_id' => $providerId,
                'avatar_path' => $avatar,
                'last_login_at' => now(),
            ]);

            $user->assignRole('student');

            Student::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['preferred_locale' => 'th']
            );

            return $user;
        }

        $user->forceFill([
            'provider' => $provider,
            'provider_id' => $providerId,
            'avatar_path' => $avatar ?: $user->avatar_path,
            'last_login_at' => now(),
        ])->save();

        if (! $user->hasRole('student') && ! $user->hasAnyRole(['super_admin', 'admin', 'consultant'])) {
            $user->assignRole('student');
        }

        Student::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_locale' => $user->locale ?? 'th']
        );

        return $user;
    }
}
