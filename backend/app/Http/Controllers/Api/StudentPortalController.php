<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Support\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $student = $this->studentOrFail($request);
        $user = $request->user();

        $applications = $student->applications()
            ->with(['university:id,slug,name_th,name_en', 'course:id,slug,name_th,name_en', 'country:id,slug,name_th,name_en'])
            ->latest()
            ->limit(5)
            ->get();

        $documents = $student->documents()->latest()->get();
        $approvedDocs = $documents->where('status', 'approved')->count();

        $upcoming = Appointment::query()
            ->where('student_id', $student->id)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $unreadNotifications = StudentNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'phone', 'locale']),
            'student' => $student,
            'stats' => [
                'applications_count' => $student->applications()->count(),
                'documents_count' => $documents->count(),
                'documents_approved' => $approvedDocs,
                'upcoming_appointments' => $upcoming->count(),
                'unread_notifications' => $unreadNotifications,
            ],
            'applications' => $applications,
            'documents' => $documents,
            'appointments' => $upcoming,
        ]);
    }

    public function applications(Request $request): JsonResponse
    {
        $student = $this->studentOrFail($request);

        $applications = $student->applications()
            ->with([
                'university:id,slug,name_th,name_en',
                'course:id,slug,name_th,name_en',
                'country:id,slug,name_th,name_en',
                'activities' => fn ($q) => $q->latest()->limit(10),
            ])
            ->latest()
            ->get();

        return response()->json(['data' => $applications]);
    }

    public function documents(Request $request): JsonResponse
    {
        $student = $this->studentOrFail($request);
        $types = DocumentType::query()->orderByDesc('is_required')->orderBy('name_en')->get();
        $docs = $student->documents()->with('type')->latest()->get();

        $checklist = $types->map(function (DocumentType $type) use ($docs) {
            $match = $docs->firstWhere('document_type_id', $type->id);

            return [
                'type' => $type,
                'document' => $match,
                'status' => $match?->status ?? 'missing',
            ];
        });

        return response()->json([
            'data' => $docs,
            'types' => $types,
            'checklist' => $checklist,
        ]);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $student = $this->studentOrFail($request);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
            'name' => ['nullable', 'string', 'max:255'],
            'document_type_id' => ['nullable', 'exists:document_types,id'],
            'application_id' => ['nullable', 'exists:applications,id'],
        ]);

        if (! empty($data['application_id'])) {
            $ownsApp = $student->applications()->where('id', $data['application_id'])->exists();
            abort_unless($ownsApp, 403);
        }

        $stored = $request->file('file')->store("students/{$student->id}/documents", Media::diskName());
        $url = Media::url($stored) ?? $stored;

        $document = Document::query()->create([
            'student_id' => $student->id,
            'application_id' => $data['application_id'] ?? null,
            'document_type_id' => $data['document_type_id'] ?? null,
            'name' => $data['name'] ?? $request->file('file')->getClientOriginalName(),
            'path' => $url,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Document uploaded',
            'data' => $document->load('type'),
            'url' => $url,
        ], 201);
    }

    public function appointments(Request $request): JsonResponse
    {
        $student = $this->studentOrFail($request);

        $appointments = Appointment::query()
            ->with('consultant:id,name')
            ->where('student_id', $student->id)
            ->orderByDesc('starts_at')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $notifications = StudentNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $notifications,
            'unread_count' => $notifications->whereNull('read_at')->count(),
        ]);
    }

    public function markNotificationRead(Request $request, StudentNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['data' => $notification->fresh()]);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        StudentNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $this->studentOrFail($request);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'in:th,en'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $user->fill(collect($data)->only(['name', 'phone', 'locale'])->all())->save();
        $student->fill(collect($data)->only(['nationality', 'education_level', 'date_of_birth', 'preferred_locale'])->all());
        if (isset($data['locale'])) {
            $student->preferred_locale = $data['locale'];
        }
        $student->save();

        return response()->json([
            'user' => $user->fresh()->load('student'),
        ]);
    }

    private function studentOrFail(Request $request): Student
    {
        $student = $request->user()->student;

        abort_unless($student, 404, 'Student profile not found');

        return $student;
    }
}
