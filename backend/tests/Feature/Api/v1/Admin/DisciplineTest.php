<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Course;
use App\Models\Discipline;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Disciplinas (admin): listar, criar, editar, excluir e acesso. */
class DisciplineTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_see_disciplines_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/api/v1/admin/disciplines');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_see_discipline_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/api/v1/admin/disciplines');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_discipline(): void
    {
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/disciplines', [
            'title' => 'Disciplina de Teste',
            'description' => 'Descrição',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('disciplines', [
            'title' => 'Disciplina de Teste',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_admin_can_see_discipline_show(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/disciplines/' . $discipline->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $discipline->title]);
    }

    public function test_admin_can_update_discipline(): void
    {
        $discipline = Discipline::factory()->create(['title' => 'Título antigo']);
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/disciplines/' . $discipline->id, [
            'title' => 'Título atualizado',
            'description' => $discipline->description,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('disciplines', ['id' => $discipline->id, 'title' => 'Título atualizado']);
    }

    public function test_admin_can_delete_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/disciplines/' . $discipline->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('disciplines', ['id' => $discipline->id]);
    }

    public function test_discipline_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/disciplines', [
            'title' => '',
            'course_id' => 99999,
            'teacher_id' => 99999,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'course_id', 'teacher_id']);
    }

    public function test_student_cannot_access_disciplines_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/disciplines');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_disciplines_index(): void
    {
        $response = $this->getJson('/api/v1/admin/disciplines');
        $response->assertStatus(401);
    }
}
