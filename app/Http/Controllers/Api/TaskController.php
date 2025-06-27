<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Jobs\SyncGoogleEvent;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Kontroler REST dla zasobu Task.
 */
class TaskController extends Controller
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Lista zadań zalogowanego użytkownika.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'priority', 'search']);
        $perPage = (int) $request->get('per_page', 15);

        $collection = $this->tasks->forUser(
            $request->user()->id,
            $filters,
            $perPage,
        );

        return TaskResource::collection($collection);
    }

    /**
     * Tworzy nowe zadanie.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data       = $request->validated();
        $shouldSync = $request->boolean('sync_calendar');
        unset($data['sync_calendar']);

        $data['user_id'] = $request->user()->id;
        $task            = $this->tasks->create($data);

        if ($shouldSync) {
            SyncGoogleEvent::dispatch($task, 'create');
        }

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Zwraca pojedyncze zadanie.
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        $task->loadMissing('user');

        return new TaskResource($task);
    }

    /**
     * Aktualizuje zadanie.
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $attributes = $request->validated();
        $shouldSync = $request->boolean('sync_calendar');
        unset($attributes['sync_calendar']);

        $this->tasks->update($task, $attributes);

        if ($shouldSync) {
            SyncGoogleEvent::dispatch($task->fresh(), 'update');
        }

        return new TaskResource($task->fresh());
    }

    /**
     * Usuwa zadanie.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        SyncGoogleEvent::dispatch($task, 'delete');
        $this->tasks->delete($task);

        return response()->json(status: 204);
    }
} 