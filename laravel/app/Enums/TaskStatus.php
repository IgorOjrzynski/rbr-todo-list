<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum TaskStatus
 *
 * Określa bieżący status zadania.
 * Stosujemy wartości tekstowe (backed enum typu string) – dzięki temu
 * w bazie danych i API utrzymujemy czytelne, samo-opisujące się wartości.
 */
enum TaskStatus: string
{
    /** Zadanie oczekuje na rozpoczęcie */
    case PENDING = 'pending';

    /** Zadanie jest w trakcie realizacji */
    case IN_PROGRESS = 'in-progress';

    /** Zadanie zostało ukończone */
    case COMPLETED = 'completed';

    /**
     * Zwraca listę wartości Enum-a.
     *
     * Przykład użycia:
     *  TaskStatus::values(); // ['pending', 'in-progress', 'completed']
     *
     * @return array<int, string> Tablica wartości enum-a
     */
    public static function values(): array
    {
        /** @var array<int, string> */
        return array_column(self::cases(), 'value');
    }
}
