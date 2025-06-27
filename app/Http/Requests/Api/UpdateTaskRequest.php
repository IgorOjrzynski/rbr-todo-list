<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Walidacja danych przy aktualizacji zadania.
 */
class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sam dostęp weryfikuje TaskPolicy
    }

    public function rules(): array
    {
        return [
            'title'         => ['sometimes', 'string', 'max:255'],
            'description'   => ['sometimes', 'nullable', 'string'],
            'due_date'      => ['sometimes', 'nullable', 'date'],
            'status'        => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority'      => ['sometimes', Rule::enum(TaskPriority::class)],
            'sync_calendar' => ['sometimes', 'boolean'],
        ];
    }
} 