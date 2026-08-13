<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety net if earlier CRM migrations partially failed on Render.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('leads', 'next_follow_up_at')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->timestamp('next_follow_up_at')->nullable()->after('last_contact_at');
                $table->index('next_follow_up_at');
            });
        }

        if (! Schema::hasColumn('applications', 'lead_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('lead_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            });

            try {
                Schema::table('applications', function (Blueprint $table) {
                    $table->unique('lead_id');
                });
            } catch (\Throwable) {
            }
        }
    }

    public function down(): void
    {
        // Non-destructive safety migration — intentionally empty.
    }
};
