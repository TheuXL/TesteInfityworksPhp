<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Alunos (admin): listar, criar, editar, excluir, validação e acesso. */
class AdminStudentTest extends TestCase
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
        $response = $this->actingAs($this->admin)->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.students.index');
    }

    public function test_admin_can_see_student_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.students.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.students.create');
    }

    public function test_admin_can_create_student(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'name' => 'Aluno Novo',
            'email' => 'aluno.novo@test.com',
            'birth_date' => '2000-05-15',
        ]);
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', ['name' => 'Aluno Novo', 'email' => 'aluno.novo@test.com']);
    }

    public function test_admin_can_see_student_show(): void
    {
        $student = Student::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.students.show', $student));
        $response->assertStatus(200);
        $response->assertViewIs('admin.students.show');
        $response->assertSee($student->name);
    }

    public function test_admin_can_update_student(): void
    {
        $student = Student::factory()->create(['name' => 'Nome antigo']);
        $response = $this->actingAs($this->admin)->put(route('admin.students.update', $student), [
            'name' => 'Nome atualizado',
            'email' => $student->email,
            'birth_date' => $student->birth_date?->format('Y-m-d'),
        ]);
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nome atualizado']);
    }

    public function test_admin_can_delete_student(): void
    {
        $student = Student::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('admin.students.destroy', $student));
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_store_validates_required_name_and_email(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'name' => '',
            'email' => 'invalid-email',
        ]);
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_student_store_rejects_duplicate_email(): void
    {
        Student::factory()->create(['email' => 'existente@test.com']);
        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'name' => 'Outro',
            'email' => 'existente@test.com',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_student_cannot_access_students_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.students.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_students_index(): void
    {
        $response = $this->get(route('admin.students.index'));
        $response->assertRedirect(route('login'));
    }
}
