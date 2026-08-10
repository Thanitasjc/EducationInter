<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('locale', 5)->default('th')->after('phone');
            $table->string('avatar_path')->nullable()->after('locale');
            $table->string('provider')->nullable()->after('avatar_path');
            $table->string('provider_id')->nullable()->after('provider');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['provider', 'provider_id']);
            $table->dropColumn([
                'phone',
                'locale',
                'avatar_path',
                'provider',
                'provider_id',
                'last_login_at',
                'is_active',
            ]);
        });
    }
};
