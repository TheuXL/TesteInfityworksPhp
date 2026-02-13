<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do dashboard do aluno: acesso permitido só para aluno com perfil. */
class AlunoDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_aluno_dashboard(): void
    {
        $user = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get(route('aluno.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('aluno.dashboard');
    }

    public function test_admin_cannot_access_aluno_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('aluno.dashboard'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_aluno_dashboard(): void
    {
        $response = $this->get(route('aluno.dashboard'));
        $response->assertRedirect(route('login'));
    }
}
