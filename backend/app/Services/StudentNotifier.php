<?php

namespace App\Services;

use App\Models\StudentNotification;
use App\Models\User;

class StudentNotifier
{
    public function notify(
        User $user,
        string $title,
        ?string $body = null,
        string $type = 'info',
        ?string $link = null,
    ): StudentNotification {
        return StudentNotification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => $link,
        ]);
    }
}
