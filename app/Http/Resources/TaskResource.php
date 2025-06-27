<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Reprezentacja API dla modelu Task.
 *
 * Udostępnia tylko wymagane pola oraz
 * zwraca użytkownika, jeśli został załadowany.
 *
 * @mixin \App\Models\Task
 */
class TaskResource extends JsonResource
{
    /**
     * Zamienia model na tablicę.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,   // enum value
            'priority'    => $this->priority, // enum value
            'due_date'    => $this->due_date,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'user'        => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
} 