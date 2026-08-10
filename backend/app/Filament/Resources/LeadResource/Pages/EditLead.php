<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Enums\LeadStatus;
use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    private ?string $originalStatus = null;

    private ?int $originalAssignee = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalStatus = $data['status'] instanceof LeadStatus
            ? $data['status']->value
            : ($data['status'] ?? null);
        $this->originalAssignee = $data['assigned_to'] ?? null;

        return $data;
    }

    protected function afterSave(): void
    {
        $lead = $this->record;
        $actor = auth()->user();

        $newStatus = $lead->status instanceof LeadStatus
            ? $lead->status->value
            : (string) $lead->status;

        if ($this->originalStatus && $this->originalStatus !== $newStatus) {
            $lead->activities()->create([
                'user_id' => $actor?->id,
                'type' => 'status_change',
                'from_status' => $this->originalStatus,
                'to_status' => $newStatus,
                'body' => 'Status updated from edit form',
            ]);
            $lead->forceFill(['last_contact_at' => now()])->save();
        }

        if (
            $this->originalAssignee !== $lead->assigned_to
            && $lead->assigned_to
            && $lead->assignee
        ) {
            $lead->activities()->create([
                'user_id' => $actor?->id,
                'type' => 'assignment',
                'body' => "Assigned to {$lead->assignee->name}",
            ]);
            $lead->forceFill(['last_contact_at' => now()])->save();
        }

        $this->originalStatus = $newStatus;
        $this->originalAssignee = $lead->assigned_to;
    }
}
