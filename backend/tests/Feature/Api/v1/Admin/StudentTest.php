<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Alunos (admin): listar, criar, editar, excluir, validação e acesso. */
class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_see_students_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/api/v1/admin/students');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_see_student_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/api/v1/admin/students');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_student(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/students', [
            'name' => 'Aluno Novo',
            'email' => 'aluno.novo@test.com',
            'birth_date' => '2000-05-15',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('students', ['name' => 'Aluno Novo', 'email' => 'aluno.novo@test.com']);
    }

    public function test_admin_can_see_student_show(): void
    {
        $student = Student::factory()->create();
        $response = $this->actingAs($this->admin)->get('/api/v1/admin/students/' . $student->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $student->name]);
    }

    public function test_admin_can_update_student(): void
    {
        $student = Student::factory()->create(['name' => 'Nome antigo']);
        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/students/' . $student->id, [
            'name' => 'Nome atualizado',
            'email' => $student->email,
            'birth_date' => $student->birth_date ? $student->birth_date->format('Y-m-d') : null,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nome atualizado']);
    }

    public function test_admin_can_delete_student(): void
    {
        $student = Student::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/students/' . $student->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_store_validates_required_name_and_email(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/students', [
            'name' => '',
            'email' => 'invalid-email',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_student_store_rejects_duplicate_email(): void
    {
        Student::factory()->create(['email' => 'existente@test.com']);
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/students', [
            'name' => 'Outro',
            'email' => 'existente@test.com',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_student_cannot_access_students_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/students');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_students_index(): void
    {
        $response = $this->getJson('/api/v1/admin/students');
        $response->assertStatus(401);
    }
}
