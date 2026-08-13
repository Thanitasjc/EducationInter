<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LeadPipelineService
{
    public function __construct(
        protected CrmNotifier $crmNotifier,
    ) {}

    public function changeStatus(Lead $lead, LeadStatus|string $status, ?User $actor = null, ?string $note = null): Lead
    {
        $to = $status instanceof LeadStatus ? $status : LeadStatus::from($status);
        $from = $lead->status instanceof LeadStatus ? $lead->status->value : (string) $lead->status;

        if ($from === $to->value) {
            return $lead;
        }

        return DB::transaction(function () use ($lead, $from, $to, $actor, $note) {
            $lead->forceFill([
                'status' => $to,
                'last_contact_at' => now(),
            ])->save();

            LeadActivity::query()->create([
                'lead_id' => $lead->id,
                'user_id' => $actor?->id,
                'type' => 'status_change',
                'from_status' => $from,
                'to_status' => $to->value,
                'body' => $note ?: "Status changed to {$to->value}",
            ]);

            return $lead->fresh();
        });
    }

    public function assign(Lead $lead, User $consultant, ?User $actor = null): Lead
    {
        return DB::transaction(function () use ($lead, $consultant, $actor) {
            $lead->forceFill([
                'assigned_to' => $consultant->id,
                'last_contact_at' => now(),
            ])->save();

            LeadActivity::query()->create([
                'lead_id' => $lead->id,
                'user_id' => $actor?->id,
                'type' => 'assignment',
                'body' => "Assigned to {$consultant->name}",
            ]);

            return $lead->fresh(['assignee']);
        });
    }

    public function addNote(Lead $lead, string $body, ?User $actor = null): LeadActivity
    {
        $lead->forceFill(['last_contact_at' => now()])->save();

        return LeadActivity::query()->create([
            'lead_id' => $lead->id,
            'user_id' => $actor?->id,
            'type' => 'note',
            'body' => $body,
        ]);
    }

    public function convertToApplication(Lead $lead, ?User $actor = null): Application
    {
        if (! filled($lead->email)) {
            throw new InvalidArgumentException('Lead email is required to create an application.');
        }

        $existing = Application::query()->where('lead_id', $lead->id)->first();
        if ($existing) {
            return $existing->load(['student.user', 'country', 'university', 'course', 'lead']);
        }

        if (preg_match('/WIN-\d{6}-[A-Z0-9]+/', (string) $lead->notes, $matches)) {
            $byNo = Application::query()->where('application_no', $matches[0])->first();
            if ($byNo) {
                if (! $byNo->lead_id) {
                    $byNo->forceFill(['lead_id' => $lead->id])->save();
                }

                return $byNo->load(['student.user', 'country', 'university', 'course', 'lead']);
            }
        }

        return DB::transaction(function () use ($lead, $actor) {
            $user = User::query()->firstOrCreate(
                ['email' => $lead->email],
                [
                    'name' => $lead->name,
                    'phone' => $lead->phone,
                    'locale' => 'th',
                    'password' => Hash::make(Str::random(32)),
                    'is_active' => true,
                ]
            );

            if (! $user->hasRole('student')) {
                $user->assignRole('student');
            }

            $student = $lead->student_id
                ? Student::query()->find($lead->student_id)
                : null;

            $student ??= Student::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['preferred_locale' => 'th']
            );

            $applicationNo = 'WIN-'.now()->format('ymd').'-'.Str::upper(Str::random(5));

            $application = Application::query()->create([
                'application_no' => $applicationNo,
                'student_id' => $student->id,
                'lead_id' => $lead->id,
                'consultant_id' => $lead->assigned_to,
                'country_id' => $lead->country_id,
                'university_id' => $lead->university_id,
                'course_id' => $lead->course_id,
                'status' => ApplicationStatus::Consultation,
                'next_action' => 'Complete documents',
                'current_step' => 1,
                'personal_data' => [
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'from_lead_id' => $lead->id,
                ],
            ]);

            ApplicationActivity::query()->create([
                'application_id' => $application->id,
                'user_id' => $actor?->id,
                'type' => 'created_from_lead',
                'to_status' => ApplicationStatus::Consultation->value,
                'body' => "Created from lead #{$lead->id}",
            ]);

            $lead->forceFill([
                'student_id' => $student->id,
                'status' => LeadStatus::Application,
                'notes' => trim(($lead->notes ? $lead->notes."\n" : '').'Linked application: '.$applicationNo),
                'last_contact_at' => now(),
            ])->save();

            LeadActivity::query()->create([
                'lead_id' => $lead->id,
                'user_id' => $actor?->id,
                'type' => 'converted',
                'to_status' => LeadStatus::Application->value,
                'body' => "Converted to application {$applicationNo}",
            ]);

            $application = $application->fresh(['student.user', 'country', 'university', 'course', 'lead']);
            $this->crmNotifier->leadConverted($lead->fresh(), $application);

            return $application;
        });
    }

    /**
     * @param  array{title: string, starts_at: mixed, ends_at?: mixed, type?: string, notes?: string, consultant_id?: int}  $data
     */
    public function scheduleAppointment(Lead $lead, array $data, ?User $actor = null): Appointment
    {
        return DB::transaction(function () use ($lead, $data, $actor) {
            $consultantId = $data['consultant_id'] ?? $lead->assigned_to ?? $actor?->id;

            $appointment = Appointment::query()->create([
                'lead_id' => $lead->id,
                'student_id' => $lead->student_id,
                'consultant_id' => $consultantId,
                'type' => $data['type'] ?? 'consultation',
                'title' => $data['title'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'] ?? null,
                'status' => 'scheduled',
                'notes' => $data['notes'] ?? null,
            ]);

            if ($consultantId && ! $lead->assigned_to) {
                $lead->forceFill(['assigned_to' => $consultantId])->save();
            }

            $current = $lead->status instanceof LeadStatus ? $lead->status->value : (string) $lead->status;
            if (in_array($current, ['new', 'contacted'], true)) {
                $this->changeStatus($lead, LeadStatus::Consultation, $actor, 'Appointment scheduled');
            } else {
                $lead->forceFill(['last_contact_at' => now()])->save();
                LeadActivity::query()->create([
                    'lead_id' => $lead->id,
                    'user_id' => $actor?->id,
                    'type' => 'appointment',
                    'body' => "Appointment scheduled: {$appointment->title}",
                ]);
            }

            $this->crmNotifier->appointmentBooked($appointment->fresh(['lead', 'consultant', 'student.user']));

            return $appointment;
        });
    }
}
