<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('lead_id')
                ->nullable()
                ->after('student_id')
                ->constrained()
                ->nullOnDelete();
            $table->unique('lead_id');
        });

        $apps = DB::table('applications')->select('id', 'application_no', 'personal_data')->get();
        foreach ($apps as $app) {
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
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique(['lead_id']);
            $table->dropConstrainedForeignId('lead_id');
        });
    }
};
