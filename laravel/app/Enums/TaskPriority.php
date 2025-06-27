<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum TaskPriority
 *
 * Reprezentuje priorytet przypisany do zadania.
 * Korzystamy z łańcuchów znaków, aby przechowywać czytelne etykiety
 * w bazie danych oraz zwracać je w odpowiedziach API.
 */
enum TaskPriority: string
{
    /** Niski priorytet – zadanie może poczekać */
    case LOW = 'low';

    /** Średni priorytet – zadanie powinno być wykonane w terminie */
    case MEDIUM = 'medium';

    /** Wysoki priorytet – zadanie wymaga szybkiej realizacji */
    case HIGH = 'high';

    /**
     * Zwraca listę wartości Enum-a.
     *
     * Przykład użycia:
     *  TaskPriority::values(); // ['low', 'medium', 'high']
     *
     * @return array<int, string> Tablica wartości enum-a
     */
    public static function values(): array
    {
        /** @var array<int, string> */
        return array_column(self::cases(), 'value');
    }
}
