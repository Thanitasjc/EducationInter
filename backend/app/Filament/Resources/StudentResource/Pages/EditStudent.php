<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function (Student $record): void {
                    $record->user?->delete();
                }),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Student $record */
        $record = $this->getRecord();
        $user = $record->user;

        $data['user_name'] = $user?->name;
        $data['user_email'] = $user?->email;
        $data['user_phone'] = $user?->phone;
        $data['user_locale'] = $user?->locale ?? 'th';
        $data['user_is_active'] = $user?->is_active ?? true;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Student $record */
        $emailTaken = User::query()
            ->where('email', $data['user_email'])
            ->where('id', '!=', $record->user_id)
            ->exists();

        if ($emailTaken) {
            throw ValidationException::withMessages([
                'user_email' => 'อีเมลนี้ถูกใช้แล้ว',
            ]);
        }

        return DB::transaction(function () use ($record, $data) {
            $userData = [
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'phone' => $data['user_phone'] ?? null,
                'locale' => $data['user_locale'] ?? 'th',
                'is_active' => (bool) ($data['user_is_active'] ?? true),
            ];

            if (! empty($data['user_password'])) {
                $userData['password'] = $data['user_password'];
            }

            $record->user?->update($userData);

            $record->update([
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'education_level' => $data['education_level'] ?? null,
                'preferred_locale' => $data['preferred_locale'] ?? 'th',
            ]);

            return $record->refresh()->load('user');
        });
    }
}
