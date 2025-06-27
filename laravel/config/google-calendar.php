<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ID kalendarza
    |--------------------------------------------------------------------------
    | Ustaw w .env → GOOGLE_CALENDAR_ID="primary" lub pełny e-mail kalendarza.
    */
    'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),

    /*
    |--------------------------------------------------------------------------
    | Ścieżka do poświadczeń JSON (Service Account)
    |--------------------------------------------------------------------------
    | Plik kopiujemy do storage/app/google-calendar/service-account.json
    | i przekazujemy zmienną środowiskową
    | GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/laravel/storage/app/google-calendar/service-account.json
    */
    'credentials_json' => env('GOOGLE_APPLICATION_CREDENTIALS'),
]; 