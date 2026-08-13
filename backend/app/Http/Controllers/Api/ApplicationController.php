<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Models\Document;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Student;
use App\Models\User;
use App\Services\CrmNotifier;
use App\Support\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function store(Request $request, CrmNotifier $crmNotifier): JsonResponse
    {
        if (is_string($request->input('education_history'))) {
            $decoded = json_decode($request->input('education_history'), true);
            $request->merge([
                'education_history' => is_array($decoded) ? $decoded : null,
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'in:th,en'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'university_id' => ['nullable', 'exists:universities,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'intake' => ['nullable', 'string', 'max:50'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'education_history' => ['nullable', 'array'],
            'message' => ['nullable', 'string', 'max:5000'],
            'documents_note' => ['nullable', 'string', 'max:2000'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*.file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
            'documents.*.document_type_id' => ['nullable', 'exists:document_types,id'],
            'documents.*.name' => ['nullable', 'string', 'max:255'],
        ]);

        $uploadedDocs = $request->file('documents', []);

        $result = DB::transaction(function () use ($data, $uploadedDocs) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'] ?? null,
                    'locale' => $data['locale'] ?? 'th',
                    'password' => Hash::make(Str::random(32)),
                    'is_active' => true,
                ]
            );

            if (! $user->hasRole('student')) {
                $user->assignRole('student');
            }

            $student = Student::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'education_level' => $data['education_level'] ?? null,
                    'education_history' => $data['education_history'] ?? null,
                    'preferred_locale' => $data['locale'] ?? 'th',
                ]
            );

            $applicationNo = 'WIN-'.now()->format('ymd').'-'.Str::upper(Str::random(5));

            $lead = Lead::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'source' => 'website',
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'university_id' => $data['university_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'status' => LeadStatus::Submitted,
                'student_id' => $student->id,
                'message' => $data['message'] ?? 'Submitted via /apply',
                'notes' => 'Linked application: '.$applicationNo,
                'last_contact_at' => now(),
            ]);

            LeadActivity::query()->create([
                'lead_id' => $lead->id,
                'type' => 'created',
                'to_status' => LeadStatus::Submitted->value,
                'body' => "Lead created from application {$applicationNo}",
            ]);

            $application = Application::query()->create([
                'application_no' => $applicationNo,
                'student_id' => $student->id,
                'lead_id' => $lead->id,
                'country_id' => $data['country_id'] ?? null,
                'university_id' => $data['university_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'intake' => $data['intake'] ?? null,
                'status' => ApplicationStatus::Submitted,
                'next_action' => 'Consultant review',
                'current_step' => 6,
                'personal_data' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'documents_note' => $data['documents_note'] ?? null,
                    'from_lead_id' => $lead->id,
                ],
                'education_data' => [
                    'education_level' => $data['education_level'] ?? null,
                    'education_history' => $data['education_history'] ?? null,
                ],
                'submitted_at' => now(),
            ]);

            ApplicationActivity::query()->create([
                'application_id' => $application->id,
                'type' => 'submitted',
                'to_status' => ApplicationStatus::Submitted->value,
                'body' => 'Application submitted from website /apply',
            ]);

            $documentCount = 0;
            foreach ($uploadedDocs as $index => $item) {
                $file = is_array($item) ? ($item['file'] ?? null) : null;
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $meta = $data['documents'][$index] ?? [];
                $stored = $file->store("students/{$student->id}/documents", Media::diskName());
                $url = Media::url($stored) ?? $stored;

                Document::query()->create([
                    'student_id' => $student->id,
                    'application_id' => $application->id,
                    'document_type_id' => $meta['document_type_id'] ?? null,
                    'name' => $meta['name'] ?? $file->getClientOriginalName(),
                    'path' => $url,
                    'status' => 'pending',
                ]);
                $documentCount++;
            }

            if ($documentCount > 0) {
                ApplicationActivity::query()->create([
                    'application_id' => $application->id,
                    'type' => 'documents_uploaded',
                    'body' => "Uploaded {$documentCount} document(s) with application",
                ]);
            }

            return [
                'application' => $application->load(['country', 'university', 'course', 'documents', 'lead']),
                'lead' => $lead,
                'user' => $user,
            ];
        });

        try {
            $crmNotifier->applicationSubmitted($result['application'], $result['lead']);
        } catch (\Throwable) {
            // Apply must succeed even if staff/student notifications fail.
        }

        $claimToken = null;
        try {
            $claimToken = Password::broker()->createToken($result['user']);
        } catch (\Throwable) {
            // Claim link is optional if token table unavailable.
        }

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $result['application'],
            'lead_id' => $result['lead']->id,
            'claim' => [
                'email' => $result['user']->email,
                'token' => $claimToken,
            ],
        ], 201);
    }
}
