<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SyncGoogleEvent implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly string $action // create|update|delete
    ) {}

    public function handle(GoogleCalendarService $calendar): void
    {
        match ($this->action) {
            'create' => $calendar->create($this->task),
            'update' => $calendar->update($this->task),
            'delete' => $calendar->delete($this->task),
        };
    }
} 