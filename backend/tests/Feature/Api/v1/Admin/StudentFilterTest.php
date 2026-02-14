<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do filtro de alunos por nome e e-mail na API v1. */
class StudentFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_students_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        Student::factory()->create(['name' => 'João Silva', 'email' => 'joao@test.com']);
        Student::factory()->create(['name' => 'Maria Santos', 'email' => 'maria@test.com']);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/students?search=João');
        $response->assertStatus(200);
        $data = $response->json('data');
        $names = array_column($data, 'name');
        $this->assertContains('João Silva', $names);
        $this->assertNotContains('Maria Santos', $names);
    }

    public function test_admin_can_filter_students_by_email(): void
    {
        $admin = User::factory()->admin()->create();
        Student::factory()->create(['name' => 'João', 'email' => 'joao.unique@test.com']);
        Student::factory()->create(['name' => 'Maria', 'email' => 'maria.unique@test.com']);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/students?search=joao.unique');
        $response->assertStatus(200);
        $data = $response->json('data');
        $emails = array_column($data, 'email');
        $this->assertContains('joao.unique@test.com', $emails);
    }
}
