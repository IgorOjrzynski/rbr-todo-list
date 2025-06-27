<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Polityka autoryzacji dla modelu Task.
 *
 * Pozwala użytkownikowi zarządzać wyłącznie własnymi zadaniami.
 */
class TaskPolicy
{
    /**
     * Czy użytkownik może wyświetlać listę zadań.
     *
     * @param User $user Aktualnie uwierzytelniony użytkownik
     */
    public function viewAny(User $user): bool
    {
        return true; // lista jest filtrowana po user_id w repozytorium
    }

    /**
     * Czy użytkownik może wyświetlić konkretne zadanie.
     */
    public function view(User $user, Task $task): bool|Response
    {
        return $user->id === $task->user_id;
    }

    /**
     * Czy użytkownik może utworzyć zadanie.
     */
    public function create(User $user): bool
    {
        return true; // wystarczy zalogowany użytkownik
    }

    /**
     * Czy użytkownik może zaktualizować zadanie.
     */
    public function update(User $user, Task $task): bool|Response
    {
        return $user->id === $task->user_id;
    }

    /**
     * Czy użytkownik może usunąć zadanie.
     */
    public function delete(User $user, Task $task): bool|Response
    {
        return $user->id === $task->user_id;
    }
} 