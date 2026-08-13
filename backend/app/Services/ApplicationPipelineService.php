<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Models\DocumentType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApplicationPipelineService
{
    public function __construct(
        protected StudentNotifier $studentNotifier,
        protected CrmNotifier $crmNotifier,
        protected LeadPipelineService $leadPipeline,
    ) {}

    public function changeStatus(
        Application $application,
        ApplicationStatus|string $status,
        ?User $actor = null,
        ?string $note = null,
        ?string $nextAction = null,
        bool $notifyStudent = true,
    ): Application {
        $to = $status instanceof ApplicationStatus ? $status : ApplicationStatus::from($status);
        $from = $application->status instanceof ApplicationStatus
            ? $application->status->value
            : (string) $application->status;

        if ($from === $to->value && $nextAction === null) {
            return $application;
        }

        return DB::transaction(function () use ($application, $from, $to, $actor, $note, $nextAction, $notifyStudent) {
            $payload = ['status' => $to];
            if ($nextAction !== null) {
                $payload['next_action'] = $nextAction;
            }
            if ($to === ApplicationStatus::Submitted && ! $application->submitted_at) {
                $payload['submitted_at'] = now();
            }

            $application->forceFill($payload)->save();

            if ($from !== $to->value) {
                ApplicationActivity::query()->create([
                    'application_id' => $application->id,
                    'user_id' => $actor?->id,
                    'type' => 'status_change',
                    'from_status' => $from,
                    'to_status' => $to->value,
                    'body' => $note ?: "Status changed to {$to->value}",
                ]);
            }

            $this->syncLinkedLead($application->fresh(), $actor);

            if ($notifyStudent && $from !== $to->value) {
                $user = $application->student?->user;
                if ($user) {
                    $this->studentNotifier->notify(
                        $user,
                        'Application status updated',
                        "Application {$application->application_no} is now {$to->value}.",
                        'info',
                        '/student/applications',
                    );
                }

                $this->crmNotifier->applicationStatusChanged($application, $from, $to->value);
            }

            return $application->fresh(['student.user', 'lead', 'consultant']);
        });
    }

    public function assign(Application $application, User $consultant, ?User $actor = null): Application
    {
        return DB::transaction(function () use ($application, $consultant, $actor) {
            $application->forceFill(['consultant_id' => $consultant->id])->save();

            ApplicationActivity::query()->create([
                'application_id' => $application->id,
                'user_id' => $actor?->id,
                'type' => 'assignment',
                'body' => "Assigned to {$consultant->name}",
            ]);

            if ($application->lead_id) {
                $lead = Lead::query()->find($application->lead_id);
                if ($lead) {
                    $this->leadPipeline->assign($lead, $consultant, $actor);
                }
            }

            return $application->fresh(['consultant', 'lead']);
        });
    }

    public function setNextAction(Application $application, string $nextAction, ?User $actor = null): Application
    {
        $application->forceFill(['next_action' => $nextAction])->save();

        ApplicationActivity::query()->create([
            'application_id' => $application->id,
            'user_id' => $actor?->id,
            'type' => 'next_action',
            'body' => "Next action: {$nextAction}",
        ]);

        return $application->fresh();
    }

    /**
     * After document approve/reject: move app to ready_to_apply or document_required.
     */
    public function syncFromDocumentChecklist(Application $application, ?User $actor = null): Application
    {
        $requiredTypeIds = DocumentType::query()
            ->where('is_required', true)
            ->pluck('id');

        if ($requiredTypeIds->isEmpty()) {
            return $application;
        }

        $approvedTypeIds = $application->documents()
            ->where('status', 'approved')
            ->whereIn('document_type_id', $requiredTypeIds)
            ->pluck('document_type_id')
            ->unique();

        $hasRejectedRequired = $application->documents()
            ->where('status', 'rejected')
            ->whereIn('document_type_id', $requiredTypeIds)
            ->exists();

        $current = $application->status instanceof ApplicationStatus
            ? $application->status
            : ApplicationStatus::tryFrom((string) $application->status);

        $terminal = [
            ApplicationStatus::ConditionalOffer,
            ApplicationStatus::UnconditionalOffer,
            ApplicationStatus::Visa,
            ApplicationStatus::Completed,
            ApplicationStatus::Rejected,
            ApplicationStatus::Cancelled,
        ];

        if ($current && in_array($current, $terminal, true)) {
            return $application;
        }

        if ($hasRejectedRequired || $approvedTypeIds->count() < $requiredTypeIds->count()) {
            if ($current !== ApplicationStatus::DocumentRequired) {
                return $this->changeStatus(
                    $application,
                    ApplicationStatus::DocumentRequired,
                    $actor,
                    'Document checklist incomplete',
                    'Upload / revise required documents',
                    notifyStudent: false,
                );
            }

            return $application;
        }

        if ($current !== ApplicationStatus::ReadyToApply) {
            return $this->changeStatus(
                $application,
                ApplicationStatus::ReadyToApply,
                $actor,
                'All required documents approved',
                'Submit to university',
            );
        }

        return $application;
    }

    protected function syncLinkedLead(Application $application, ?User $actor): void
    {
        if (! $application->lead_id) {
            return;
        }

        $lead = Lead::query()->find($application->lead_id);
        if (! $lead) {
            return;
        }

        $appStatus = $application->status instanceof ApplicationStatus
            ? $application->status
            : ApplicationStatus::tryFrom((string) $application->status);

        if (! $appStatus) {
            return;
        }

        $leadStatus = match ($appStatus) {
            ApplicationStatus::Draft, ApplicationStatus::Consultation => LeadStatus::Consultation,
            ApplicationStatus::DocumentRequired => LeadStatus::Document,
            ApplicationStatus::ReadyToApply => LeadStatus::Application,
            ApplicationStatus::Submitted => LeadStatus::Submitted,
            ApplicationStatus::ConditionalOffer, ApplicationStatus::UnconditionalOffer => LeadStatus::Offer,
            ApplicationStatus::Visa => LeadStatus::Visa,
            ApplicationStatus::Completed => LeadStatus::Success,
            ApplicationStatus::Rejected, ApplicationStatus::Cancelled => LeadStatus::Lost,
        };

        $current = $lead->status instanceof LeadStatus ? $lead->status : LeadStatus::tryFrom((string) $lead->status);
        $force = in_array($leadStatus, [LeadStatus::Lost, LeadStatus::Success], true);

        if (! $force && $current && $this->leadRank($leadStatus) <= $this->leadRank($current)) {
            return;
        }

        $this->leadPipeline->changeStatus(
            $lead,
            $leadStatus,
            $actor,
            "Synced from application {$application->application_no}",
        );
    }

    protected function leadRank(LeadStatus $status): int
    {
        return match ($status) {
            LeadStatus::New => 1,
            LeadStatus::Contacted => 2,
            LeadStatus::Consultation => 3,
            LeadStatus::Interested => 4,
            LeadStatus::Document => 5,
            LeadStatus::Application => 6,
            LeadStatus::Submitted => 7,
            LeadStatus::Offer => 8,
            LeadStatus::Visa => 9,
            LeadStatus::Success => 10,
            LeadStatus::Lost => 0,
        };
    }
}
