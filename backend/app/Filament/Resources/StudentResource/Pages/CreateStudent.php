<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        if (User::query()->where('email', $data['user_email'])->exists()) {
            throw ValidationException::withMessages([
                'user_email' => 'อีเมลนี้ถูกใช้แล้ว',
            ]);
        }

        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'phone' => $data['user_phone'] ?? null,
                'password' => $data['user_password'],
                'locale' => $data['user_locale'] ?? 'th',
                'is_active' => (bool) ($data['user_is_active'] ?? true),
            ]);

            $user->assignRole('student');

            return Student::query()->create([
                'user_id' => $user->id,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'education_level' => $data['education_level'] ?? null,
                'preferred_locale' => $data['preferred_locale'] ?? 'th',
            ]);
        });
    }
}
