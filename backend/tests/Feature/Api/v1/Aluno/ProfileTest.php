<?php

namespace Tests\Feature\Api\v1\Aluno;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do perfil do aluno via API v1: editar, atualizar dados e controle de acesso. */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_profile_edit(): void
    {
        $user = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $user->id, 'name' => 'Emanuel', 'email' => 'emanuel@test.com']);
        $response = $this->actingAs($user)->getJson('/api/v1/aluno/profile');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Emanuel']);
    }

    public function test_student_can_update_profile(): void
    {
        $user = User::factory()->student()->create(['name' => 'Antigo', 'email' => 'antigo@test.com']);
        $student = Student::factory()->create(['user_id' => $user->id, 'name' => 'Antigo', 'email' => 'antigo@test.com']);
        $response = $this->actingAs($user)->putJson('/api/v1/aluno/profile', [
            'name' => 'Nome Atualizado',
            'email' => 'novo@test.com',
            'birth_date' => '2000-01-15',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nome Atualizado', 'email' => 'novo@test.com']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Atualizado', 'email' => 'novo@test.com']);
    }

    public function test_profile_update_validates_required_name_and_email(): void
    {
        $user = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->putJson('/api/v1/aluno/profile', [
            'name' => '',
            'email' => 'invalid',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_student_without_student_record_redirects_from_profile_edit(): void
    {
        $user = User::factory()->student()->create();
        $response = $this->actingAs($user)->getJson('/api/v1/aluno/profile');
        $response->assertStatus(404);
    }

    public function test_admin_cannot_access_aluno_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/api/v1/aluno/profile');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_aluno_profile(): void
    {
        $response = $this->getJson('/api/v1/aluno/profile');
        $response->assertStatus(401);
    }
}
