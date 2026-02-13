<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do perfil do aluno: editar, atualizar dados e controle de acesso. */
class AlunoProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_profile_edit(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id, 'name' => 'Emanuel', 'email' => 'emanuel@test.com']);
        $response = $this->actingAs($user)->get(route('aluno.profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('aluno.profile.edit');
        $response->assertSee('Emanuel');
    }

    public function test_student_can_update_profile(): void
    {
        $user = User::factory()->student()->create(['name' => 'Antigo', 'email' => 'antigo@test.com']);
        $student = Student::factory()->create(['user_id' => $user->id, 'name' => 'Antigo', 'email' => 'antigo@test.com']);
        $response = $this->actingAs($user)->put(route('aluno.profile.update'), [
            'name' => 'Nome Atualizado',
            'email' => 'novo@test.com',
            'birth_date' => '2000-01-15',
        ]);
        $response->assertRedirect(route('aluno.profile.edit'));
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nome Atualizado', 'email' => 'novo@test.com']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Atualizado', 'email' => 'novo@test.com']);
    }

    public function test_profile_update_validates_required_name_and_email(): void
    {
        $user = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->put(route('aluno.profile.update'), [
            'name' => '',
            'email' => 'invalid',
        ]);
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_student_without_student_record_redirects_from_profile_edit(): void
    {
        $user = User::factory()->student()->create();
        // user has no linked Student (user_id null on any student)
        $response = $this->actingAs($user)->get(route('aluno.profile.edit'));
        $response->assertRedirect(route('aluno.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_access_aluno_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('aluno.profile.edit'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_aluno_profile(): void
    {
        $response = $this->get(route('aluno.profile.edit'));
        $response->assertRedirect(route('login'));
    }
}
