<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('next_follow_up_at')->nullable()->after('last_contact_at');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['next_follow_up_at']);
            $table->dropColumn('next_follow_up_at');
        });
    }
};
