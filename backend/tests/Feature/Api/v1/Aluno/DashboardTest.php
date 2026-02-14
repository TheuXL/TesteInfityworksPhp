<?php

namespace Tests\Feature\Api\v1\Aluno;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do dashboard do aluno via API v1: acesso permitido só para aluno. */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_aluno_dashboard(): void
    {
        $user = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->getJson('/api/v1/aluno/dashboard');
        $response->assertStatus(200);
        $response->assertJsonStructure(['chart_data']);
    }

    public function test_admin_cannot_access_aluno_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/api/v1/aluno/dashboard');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_aluno_dashboard(): void
    {
        $response = $this->getJson('/api/v1/aluno/dashboard');
        $response->assertStatus(401);
    }
}
