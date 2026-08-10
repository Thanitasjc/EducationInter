<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'content_manager',
            'consultant',
            'admission_officer',
            'visa_officer',
            'student',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}
