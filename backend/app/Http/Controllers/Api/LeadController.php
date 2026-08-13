<?php

namespace App\Http\Controllers\Api;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\CrmNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request, CrmNotifier $crmNotifier): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:50'],
            'campaign' => ['nullable', 'string', 'max:100'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
            'gclid' => ['nullable', 'string', 'max:255'],
            'fbclid' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'university_id' => ['nullable', 'exists:universities,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        $lead = Lead::query()->create([
            ...$data,
            'source' => $data['source'] ?? 'website',
            'status' => LeadStatus::New,
        ]);

        LeadActivity::query()->create([
            'lead_id' => $lead->id,
            'type' => 'created',
            'to_status' => LeadStatus::New->value,
            'body' => 'Lead created from website',
        ]);

        $crmNotifier->leadCreated($lead);

        return response()->json([
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }
}
