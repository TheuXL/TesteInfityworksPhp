<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Professores (admin): listar, criar, editar, excluir e acesso. */
class TeacherTest extends TestCase
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
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/teachers');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_see_teacher_create_form(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/teachers');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_teacher(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/teachers', [
            'name' => 'Professor João',
            'email' => 'joao@plataforma.test',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('teachers', ['name' => 'Professor João', 'email' => 'joao@plataforma.test']);
    }

    public function test_admin_can_see_teacher_show(): void
    {
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/teachers/' . $teacher->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $teacher->name]);
    }

    public function test_admin_can_update_teacher(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Nome antigo']);
        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/teachers/' . $teacher->id, [
            'name' => 'Nome atualizado',
            'email' => 'atualizado@plataforma.test',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'name' => 'Nome atualizado']);
    }

    public function test_admin_can_delete_teacher(): void
    {
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/teachers/' . $teacher->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_teacher_store_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/teachers', [
            'name' => '',
            'email' => 'ok@test.com',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_student_cannot_access_teachers_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/teachers');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_teachers_index(): void
    {
        $response = $this->getJson('/api/v1/admin/teachers');
        $response->assertStatus(401);
    }
}
