<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class DiagController extends Controller
{
    public function adminSchema(): JsonResponse
    {
        $checks = [];

        foreach ([
            'notifications' => fn () => Schema::hasTable('notifications'),
            'leads.next_follow_up_at' => fn () => Schema::hasColumn('leads', 'next_follow_up_at'),
            'applications.lead_id' => fn () => Schema::hasColumn('applications', 'lead_id'),
        ] as $key => $fn) {
            try {
                $checks[$key] = (bool) $fn();
            } catch (\Throwable $e) {
                $checks[$key] = false;
                $checks[$key.'_error'] = class_basename($e).': '.$e->getMessage();
            }
        }

        try {
            $checks['stats_ok'] = true;
            app(\App\Filament\Widgets\StatsOverview::class);
        } catch (\Throwable $e) {
            $checks['stats_ok'] = false;
            $checks['stats_error'] = class_basename($e).': '.$e->getMessage();
        }

        return response()->json([
            'ok' => ($checks['notifications'] ?? false)
                && ($checks['leads.next_follow_up_at'] ?? false)
                && ($checks['applications.lead_id'] ?? false),
            'checks' => $checks,
        ]);
    }
}
