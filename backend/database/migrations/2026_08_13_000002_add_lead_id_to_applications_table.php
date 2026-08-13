<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('applications', 'lead_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('lead_id')
                    ->nullable()
                    ->after('student_id')
                    ->constrained()
                    ->nullOnDelete();
            });
        }

        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->unique('lead_id');
            });
        } catch (\Throwable) {
            // Unique index may already exist.
        }

        if (! Schema::hasColumn('applications', 'lead_id')) {
            return;
        }

        $apps = DB::table('applications')->select('id', 'application_no', 'personal_data', 'lead_id')->get();
        foreach ($apps as $app) {
            if (! empty($app->lead_id)) {
                continue;
            }

            $leadId = null;
            $personal = json_decode($app->personal_data ?? 'null', true);
            if (is_array($personal) && ! empty($personal['from_lead_id'])) {
                $leadId = (int) $personal['from_lead_id'];
            }

            if (! $leadId && $app->application_no) {
                $leadId = DB::table('leads')
                    ->where('notes', 'like', '%Linked application: '.$app->application_no.'%')
                    ->value('id');
            }

            if ($leadId && DB::table('leads')->where('id', $leadId)->exists()) {
                $taken = DB::table('applications')->where('lead_id', $leadId)->exists();
                if (! $taken) {
                    DB::table('applications')->where('id', $app->id)->update(['lead_id' => $leadId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('applications', 'lead_id')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            try {
                $table->dropUnique(['lead_id']);
            } catch (\Throwable) {
            }
            try {
                $table->dropConstrainedForeignId('lead_id');
            } catch (\Throwable) {
                $table->dropColumn('lead_id');
            }
        });
    }
};
