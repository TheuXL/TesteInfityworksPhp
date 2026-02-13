<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Professores (admin): listar, criar, editar, excluir e acesso. */
class AdminTeacherTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_see_teachers_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.teachers.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.teachers.index');
    }

    public function test_admin_can_see_teacher_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.teachers.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.teachers.create');
    }

    public function test_admin_can_create_teacher(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.teachers.store'), [
            'name' => 'Professor João',
            'email' => 'joao@plataforma.test',
        ]);
        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['name' => 'Professor João', 'email' => 'joao@plataforma.test']);
    }

    public function test_admin_can_see_teacher_show(): void
    {
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.teachers.show', $teacher));
        $response->assertStatus(200);
        $response->assertViewIs('admin.teachers.show');
        $response->assertSee($teacher->name);
    }

    public function test_admin_can_update_teacher(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Nome antigo']);
        $response = $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher), [
            'name' => 'Nome atualizado',
            'email' => 'atualizado@plataforma.test',
        ]);
        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'name' => 'Nome atualizado']);
    }

    public function test_admin_can_delete_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('admin.teachers.destroy', $teacher));
        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_teacher_store_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.teachers.store'), [
            'name' => '',
            'email' => 'ok@test.com',
        ]);
        $response->assertSessionHasErrors('name');
    }

    public function test_student_cannot_access_teachers_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.teachers.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_teachers_index(): void
    {
        $response = $this->get(route('admin.teachers.index'));
        $response->assertRedirect(route('login'));
    }
}
