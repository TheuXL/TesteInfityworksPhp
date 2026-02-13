<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes dos métodos isAdmin() e isStudent() do modelo User. */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_admin_returns_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($student->isAdmin());
    }

    public function test_user_is_student_returns_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $this->assertTrue($student->isStudent());
        $this->assertFalse($admin->isStudent());
    }
}
