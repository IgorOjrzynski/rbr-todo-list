<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

final class GoogleCalendarService
{
    public function create(Task $task): void
    {
        $event = Event::create($this->payload($task));
        $task->update(['google_event_id' => $event->id]);
    }

    public function update(Task $task): void
    {
        if (! $task->google_event_id) {
            $this->create($task);
            return;
        }

        $event = Event::find($task->google_event_id) ?: Event::create($this->payload($task));
        $event->update($this->payload($task));
    }

    public function delete(Task $task): void
    {
        if ($task->google_event_id && ($event = Event::find($task->google_event_id))) {
            $event->delete();
        }
    }

    /** @return array<string,mixed> */
    private function payload(Task $task): array
    {
        $start = $task->due_date ?? Carbon::now();
        return [
            'name'          => $task->title,
            'description'   => $task->description ?? '',
            'startDateTime' => $start,
            'endDateTime'   => $start->copy()->addHour(),
        ];
    }
}
