<?php

namespace App\Console\Commands;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Services\CrmNotifier;
use Illuminate\Console\Command;

class NotifyCrmFollowUps extends Command
{
    protected $signature = 'crm:notify-follow-ups';

    protected $description = 'Notify staff about due / overdue CRM lead follow-ups';

    public function handle(CrmNotifier $crmNotifier): int
    {
        $terminal = [LeadStatus::Success->value, LeadStatus::Lost->value];

        $due = Lead::query()
            ->with('assignee:id,name')
            ->whereNotIn('status', $terminal)
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNotNull('next_follow_up_at')
                        ->where('next_follow_up_at', '<=', now()->endOfDay());
                })->orWhere(function ($inner) {
                    $inner->whereNull('next_follow_up_at')
                        ->where(function ($stale) {
                            $stale->whereNull('last_contact_at')
                                ->orWhere('last_contact_at', '<=', now()->subDays(7));
                        });
                });
            })
            ->orderBy('next_follow_up_at')
            ->limit(100)
            ->get();

        if ($due->isEmpty()) {
            $this->info('No due follow-ups.');

            return self::SUCCESS;
        }

        $crmNotifier->followUpsDue($due);
        $this->info('Notified staff about '.$due->count().' follow-up(s).');

        return self::SUCCESS;
    }
}
