<?php

namespace App\Filament\Resources\ConsultantResource\Pages;

use App\Filament\Resources\ConsultantResource;
use App\Models\Consultant;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateConsultant extends CreateRecord
{
    protected static string $resource = ConsultantResource::class;

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
                'locale' => 'th',
                'is_active' => (bool) ($data['user_is_active'] ?? true),
            ]);

            $user->assignRole('consultant');

            return Consultant::query()->create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'max_leads' => $data['max_leads'] ?? 40,
                'is_available' => (bool) ($data['is_available'] ?? true),
            ]);
        });
    }
}
