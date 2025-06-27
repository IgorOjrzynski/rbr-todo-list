<?php

declare(strict_types=1);

namespace Tests\E2E;

use Faker\Factory as Faker;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

final class TaskApiE2ETest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        /**  Guzzle  */
        $this->client = new Client([
            'base_uri'    => getenv('E2E_BASE_URI') ?: 'http://nginx',
            'http_errors' => false,            // nie rzucaj wyjątków na 4xx/5xx
            'headers'     => [
                'Accept' => 'application/json',
            ],
            // -- tu można dodać Bearer token jeśli API wymaga autoryzacji
            // 'headers' => ['Authorization' => 'Bearer '.getenv('E2E_TOKEN')],
        ]);
    }

    public function test_create_list_delete_task(): void
    {
        $faker = Faker::create();

        /* -------------------- CREATE -------------------- */
        $payload = [
            'title'       => $faker->sentence(4),
            'description' => $faker->paragraph,
            'status'      => 'pending',
            'priority'    => 'low',
        ];

        $create = $this->client->post('/api/tasks', ['json' => $payload]);

        $this->assertSame(201, $create->getStatusCode(), 'Task create failed');

        $data    = json_decode((string) $create->getBody(), true);
        $task_id = $data['data']['id'] ?? null;

        $this->assertNotNull($task_id, 'ID not returned after create');

        /* -------------------- LIST -------------------- */
        $list = $this->client->get('/api/tasks');

        $this->assertSame(200, $list->getStatusCode(), 'Task list failed');

        $list_json = json_decode((string) $list->getBody(), true);
        $this->assertGreaterThan(
            0,
            $list_json['meta']['total'] ?? 0,
            'Created task not visible on list',
        );

        /* -------------------- DELETE -------------------- */
        $delete = $this->client->delete("/api/tasks/{$task_id}");

        $this->assertSame(204, $delete->getStatusCode(), 'Task delete failed');
    }
} 