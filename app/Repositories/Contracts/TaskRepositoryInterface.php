<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interfejs repozytorium zadań.
 *
 * Definiuje operacje CRUD oraz pobieranie listy zadań
 * dla konkretnego użytkownika wraz z opcjonalnym filtrowaniem.
 */
interface TaskRepositoryInterface
{
    /**
     * Zwraca spaginowaną listę zadań użytkownika.
     *
     * Dostępne filtry:
     *  - status   (string)
     *  - priority (string)
     *  - search   (string, wyszukiwanie po tytule)
     *
     * @param int   $user_id  Identyfikator właściciela zadań
     * @param array $filters  Tablica filtrów
     * @param int   $per_page Ilość rekordów na stronę
     *
     * @return LengthAwarePaginator<Task>
     */
    public function forUser(int $user_id, array $filters = [], int $per_page = 15): LengthAwarePaginator;

    /**
     * Pobiera pojedyncze zadanie.
     *
     * @param int $id Identyfikator zadania
     *
     * @return Task|null
     */
    public function find(int $id): ?Task;

    /**
     * Tworzy nowe zadanie.
     *
     * @param array<string,mixed> $attributes Dane zadania
     *
     * @return Task
     */
    public function create(array $attributes): Task;

    /**
     * Aktualizuje zadanie.
     *
     * @param Task                $task       Model do aktualizacji
     * @param array<string,mixed> $attributes Nowe dane
     *
     * @return bool
     */
    public function update(Task $task, array $attributes): bool;

    /**
     * Usuwa zadanie.
     *
     * @param Task $task Model do usunięcia
     *
     * @return bool
     */
    public function delete(Task $task): bool;
} 