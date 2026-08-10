<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadPipelineService
{
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
}
