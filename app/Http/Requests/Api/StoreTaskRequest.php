<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Walidacja danych przy tworzeniu zadania.
 */
class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // szczegółowa autoryzacja w TaskPolicy
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'due_date'      => ['nullable', 'date'],
            'status'        => ['required', Rule::enum(TaskStatus::class)],
            'priority'      => ['required', Rule::enum(TaskPriority::class)],
            'sync_calendar' => ['sometimes', 'boolean'],
        ];
    }
} 