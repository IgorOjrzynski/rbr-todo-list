<?php

namespace Tests\Unit\Enums;

use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    /** @test */
    public function values_method_returns_expected_set(): void
    {
        $this->assertSame(['pending', 'in-progress', 'completed'], TaskStatus::values());
    }

    /** @test */
    public function enum_contains_exactly_three_cases(): void
    {
        $this->assertCount(3, TaskStatus::cases());
    }
} 