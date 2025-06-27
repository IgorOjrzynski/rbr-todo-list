# Laravel Todo API + Vue Frontend

Projekt demonstracyjny prezentujący kompletny stack:
* **Laravel 11 (PHP 8.3)** – REST API  
* **Vue 3 + Pinia + Vite** – SPA  
* **Docker Compose** – środowisko uruchomieniowe  
* **MySQL 8 · Redis 6 · Nginx**  
* Formatowanie – Laravel Pint • Statyczna analiza – PHPStan/Larastan • Testy – PHPUnit

---

## Spis treści
1. [Wymagania](#wymagania)  
2. [Instalacja i uruchomienie](#instalacja-i-uruchomienie)  
3. [Polecenia Make](#polecenia-make)  
4. [Struktura bazy danych](#struktura-bazy-danych)  
5. [Dokumentacja API](#dokumentacja-api)  
6. [Testy i jakość kodu](#testy-i-jakość-kodu)

---

## Wymagania
* Docker Desktop (≥ 20) z włączoną integracją WSL  
* GNU make `sudo apt install make`  
* Porty 8085 (Nginx), 3310 (MySQL), 6379 (Redis) wolne na hoście

---

## Instalacja i uruchomienie

```bash
git clone https://github.com/your-org/rbr-todo-list.git
cd rbr-todo-list

# 1. Skopiuj przykładowe zmienne środowiskowe
cp laravel/.env.example laravel/.env

# 2. Wygeneruj klucz aplikacji
make sh
php artisan key:generate
exit

# 3. Uruchom cały stack (kontenery, composer install, npm install)
make up

# 4. Wykonaj migracje oraz – opcjonalnie – seedy
make sh
php artisan migrate --seed
exit

# 5. Frontend – tryb dev (hot-reload)
make dev          # http://localhost:5173
```

Po kilku sekundach API dostępne pod `http://localhost:8085/api`.

---

## Polecenia Make

| Cel               | Opis                                                         |
|-------------------|--------------------------------------------------------------|
| `make up`         | uruchamia kontenery + `composer install` + `npm install`     |
| `make stop`       | pauza kontenerów                                             |
| `make down`       | usunięcie kontenerów i sieci                                 |
| `make rebuild`    | budowa obrazów od zera                                       |
| `make sh`         | shell w kontenerze PHP                                       |
| `make test`       | PHPUnit                                                      |
| `make analyse`    | Larastan / PHPStan                                           |
| `make lint`       | Pint – sprawdzenie formatowania                              |
| `make fix`        | Pint – auto-naprawa                                          |
| `make docs`       | Generacja dokumentacji phpDoc (`/docs`)                      |
| `make dev`        | Vite `npm run dev` (hot-reload)                              |
| `make build-front`| Produkcyjny build frontendu                                  |
| `make preview`    | Podgląd prod builda (`vite preview`)                         |

---

## Struktura bazy danych (`tasks`)

| Kolumna      | Typ            | Opis                                |
|--------------|----------------|-------------------------------------|
| `id`         | bigint PK      | Klucz główny                        |
| `user_id`    | bigint FK      | Właściciel zadania                  |
| `title`      | varchar(255)   | Tytuł                               |
| `description`| text *null*    | Szczegóły                           |
| `status`     | enum           | `open` · `in_progress` · `done`     |
| `priority`   | enum           | `low` · `medium` · `high`           |
| `due_date`   | date *null*    | Termin                              |
| `created_at` | timestamp      |                                     |
| `updated_at` | timestamp      |                                     |

---

## Dokumentacja API

> Każdy endpoint wymaga nagłówków  
> `Authorization: Bearer {token}`  
> `Accept: application/json`

### Lista endpointów
| Metoda | URL               | Opis                    |
|--------|-------------------|-------------------------|
| GET    | `/api/tasks`      | lista zadań             |
| POST   | `/api/tasks`      | tworzenie              |
| GET    | `/api/tasks/{id}` | szczegóły              |
| PUT    | `/api/tasks/{id}` | aktualizacja           |
| DELETE | `/api/tasks/{id}` | usuwanie               |

---

### 1. GET `/api/tasks`

Lista zadań zalogowanego użytkownika.

| Parametr | Typ   | Opis                                              |
|----------|-------|---------------------------------------------------|
| `status` | str   | `open` · `in_progress` · `done`                   |
| `priority`| str  | `low` · `medium` · `high`                         |
| `search` | str   | fragment tytułu                                   |
| `per_page`| int  | ile rekordów na stronę (domyślnie 15)            |
| `page`    | int  | numer strony (domyślnie 1)                       |

#### Przykład żądania
```http
GET /api/tasks?status=open&priority=high&page=2&per_page=10 HTTP/1.1
Authorization: Bearer {token}
Accept: application/json
```

#### Przykład odpowiedzi 200
```json
{
  "data": [
    {
      "id": 42,
      "title": "Napisać README",
      "description": "Plik README oraz API reference",
      "status": "open",
      "priority": "high",
      "due_date": "2025-07-01",
      "created_at": "2025-06-26T10:12:33.000000Z",
      "updated_at": "2025-06-26T10:12:33.000000Z",
      "user": { "id": 7, "name": "Igor" }
    }
  ],
  "links": { "first": "...", "prev": "...", "next": "...", "last": "..." },
  "meta":  { "current_page": 2, "per_page": 10, "total": 23 }
}
```

---

### 2. POST `/api/tasks`

Tworzy zadanie.

| Pole         | Typ    | Wymagane | Walidacja                                  |
|--------------|--------|----------|--------------------------------------------|
| `title`      | string | ✔︎       | `max:255`                                  |
| `description`| string | ✖︎       |                                            |
| `status`     | string | ✔︎       | enum `open` / `in_progress` / `done`       |
| `priority`   | string | ✔︎       | enum `low` / `medium` / `high`             |
| `due_date`   | date   | ✖︎       | `Y-m-d`                                    |

#### Przykład żądania
```http
POST /api/tasks HTTP/1.1
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

{
  "title": "Nowe zadanie",
  "description": "Opis",
  "status": "open",
  "priority": "medium",
  "due_date": "2025-07-10"
}
```

##### Odpowiedź 201
```json
{ "data": { "id": 43, "title": "Nowe zadanie", "status": "open", "...": "..." } }
```

##### Błąd 422 (walidacja)
```json
{
  "message": "The given data was invalid.",
  "errors": { "title": ["The title field is required."] }
}
```

---

### 3. GET `/api/tasks/{id}`

Zwraca szczegóły zadania.

* `200` – sukces  
* `404` – brak zasobu lub brak dostępu  

---

### 4. PUT `/api/tasks/{id}`

Aktualizuje zadanie (pola opcjonalne).

```http
PUT /api/tasks/43 HTTP/1.1
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

{
  "status": "done",
  "priority": "high"
}
```

##### Odpowiedź 200
```json
{ "data": { "id": 43, "status": "done", "priority": "high", "...": "..." } }
```

##### Błędy
* `403` – brak uprawnień  
* `404` – brak zadania  
* `422` – walidacja  

---

### 5. DELETE `/api/tasks/{id}`

Usuwa zadanie.

* `204 No Content` – sukces  
* `403` / `404` – jak wyżej  

##### Błąd 401 (brak tokenu)
```json
{ "message": "Unauthenticated." }
```

---

## Testy i jakość kodu

```bash
make ci          # Pint · PHPStan · PHPUnit
```

Wszystkie powyższe narzędzia uruchamiane lokalnie w kontenerach.

---

### Kontakt
W razie problemów otwórz Issue lub napisz: `dev@your-org.com`
