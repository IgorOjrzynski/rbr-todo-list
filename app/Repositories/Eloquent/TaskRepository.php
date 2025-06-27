<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Implementacja repozytorium zadań oparta na Eloquent.
 */
final class TaskRepository implements TaskRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function forUser(int $user_id, array $filters = [], int $per_page = 15): LengthAwarePaginator
    {
        $query = Task::query()
            ->with('user')
            ->where('user_id', $user_id);

        if (($filters['status'] ?? null) !== null) {
            $query->where('status', $filters['status']);
        }

        if (($filters['priority'] ?? null) !== null) {
            $query->where('priority', $filters['priority']);
        }

        if (($filters['search'] ?? null) !== null) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($per_page);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?Task
    {
        return Task::with('user')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes): Task
    {
        /** @var Task */
        return Task::create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Task $task, array $attributes): bool
    {
        return $task->update($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }
} 