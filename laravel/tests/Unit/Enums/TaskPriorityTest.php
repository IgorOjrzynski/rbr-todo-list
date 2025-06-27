<?php

namespace Tests\Unit\Enums;

use App\Enums\TaskPriority;
use PHPUnit\Framework\TestCase;

class TaskPriorityTest extends TestCase
{
    /** @test */
    public function values_method_returns_expected_set(): void
    {
        $this->assertSame(['low', 'medium', 'high'], TaskPriority::values());
    }

    /** @test */
    public function enum_contains_exactly_three_cases(): void
    {
        $this->assertCount(3, TaskPriority::cases());
    }
} 