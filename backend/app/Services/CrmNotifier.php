<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CrmNotifier
{
    public function __construct(
        protected StudentNotifier $studentNotifier,
    ) {}

    public function leadCreated(Lead $lead): void
    {
        $this->notifyStaff(
            title: 'New lead: '.$lead->name,
            body: trim(($lead->source ? "Source: {$lead->source}. " : '').($lead->message ?: '')),
            url: \App\Filament\Resources\LeadResource::getUrl('edit', ['record' => $lead]),
        );

        Log::info('CRM lead created', ['lead_id' => $lead->id, 'email' => $lead->email]);
    }

    public function applicationSubmitted(Application $application, ?Lead $lead = null): void
    {
        $this->notifyStaff(
            title: 'New application '.$application->application_no,
            body: 'Submitted from website apply form.',
            url: \App\Filament\Resources\ApplicationResource::getUrl('edit', ['record' => $application]),
        );

        $user = $application->student?->user;
        if ($user) {
            $this->studentNotifier->notify(
                $user,
                'Application received',
                "We received your application {$application->application_no}. Our consultants will contact you soon.",
                'success',
                '/student/applications',
            );
        }

        Log::info('CRM application submitted', [
            'application_id' => $application->id,
            'application_no' => $application->application_no,
            'lead_id' => $lead?->id,
        ]);
    }

    public function leadConverted(Lead $lead, Application $application): void
    {
        $user = $application->student?->user;
        if ($user) {
            $this->studentNotifier->notify(
                $user,
                'Application created',
                "Application {$application->application_no} was created from your enquiry. Please complete documents in the student portal.",
                'info',
                '/student/documents',
            );
        }

        $this->notifyStaff(
            title: 'Lead converted: '.$lead->name,
            body: "Linked application {$application->application_no}",
            url: \App\Filament\Resources\ApplicationResource::getUrl('edit', ['record' => $application]),
            onlyAssigned: $lead->assigned_to,
        );
    }

    public function applicationStatusChanged(Application $application, string $from, string $to): void
    {
        $this->notifyStaff(
            title: "Application {$application->application_no}: {$to}",
            body: "Status changed from {$from} to {$to}",
            url: \App\Filament\Resources\ApplicationResource::getUrl('edit', ['record' => $application]),
            onlyAssigned: $application->consultant_id,
        );
    }

    public function appointmentBooked(Appointment $appointment): void
    {
        $when = $appointment->starts_at?->format('Y-m-d H:i') ?? '';
        $this->notifyStaff(
            title: 'Appointment: '.$appointment->title,
            body: trim(($appointment->lead?->name ?: 'Lead').' · '.$when),
            url: \App\Filament\Resources\AppointmentResource::getUrl('edit', ['record' => $appointment]),
            onlyAssigned: $appointment->consultant_id,
        );

        $user = $appointment->student?->user;
        if ($user) {
            $this->studentNotifier->notify(
                $user,
                'Appointment scheduled',
                "{$appointment->title} on {$when}",
                'info',
                '/student/appointments',
            );
        }
    }

    protected function notifyStaff(
        string $title,
        ?string $body,
        ?string $url = null,
        ?int $onlyAssigned = null,
    ): void {
        $query = User::query()
            ->role(['super_admin', 'admin', 'admission_officer', 'consultant'])
            ->where('is_active', true);

        if ($onlyAssigned) {
            $query->where(function ($q) use ($onlyAssigned) {
                $q->where('id', $onlyAssigned)
                    ->orWhereHas('roles', fn ($r) => $r->whereIn('name', ['super_admin', 'admin', 'admission_officer']));
            });
        }

        $users = $query->get();
        if ($users->isEmpty()) {
            return;
        }

        $notification = Notification::make()
            ->title($title)
            ->body($body)
            ->success();

        if ($url) {
            $notification->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Open')
                    ->url($url),
            ]);
        }

        try {
            $notification->sendToDatabase($users);
        } catch (\Throwable $e) {
            Log::warning('CRM staff notification failed', ['error' => $e->getMessage()]);
        }
    }
}
