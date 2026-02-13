<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Discipline;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Disciplinas (admin): listar, criar, editar, excluir e acesso. */
class AdminDisciplineTest extends TestCase
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
        $response = $this->actingAs($this->admin)->get(route('admin.disciplines.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplines.index');
    }

    public function test_admin_can_see_discipline_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.disciplines.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplines.create');
    }

    public function test_admin_can_create_discipline(): void
    {
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->post(route('admin.disciplines.store'), [
            'title' => 'Disciplina de Teste',
            'description' => 'Descrição',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
        $response->assertRedirect(route('admin.disciplines.index'));
        $this->assertDatabaseHas('disciplines', [
            'title' => 'Disciplina de Teste',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_admin_can_see_discipline_show(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.disciplines.show', $discipline));
        $response->assertStatus(200);
        $response->assertViewIs('admin.disciplines.show');
        $response->assertSee($discipline->title);
    }

    public function test_admin_can_update_discipline(): void
    {
        $discipline = Discipline::factory()->create(['title' => 'Título antigo']);
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create();
        $response = $this->actingAs($this->admin)->put(route('admin.disciplines.update', $discipline), [
            'title' => 'Título atualizado',
            'description' => $discipline->description,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
        ]);
        $response->assertRedirect(route('admin.disciplines.index'));
        $this->assertDatabaseHas('disciplines', ['id' => $discipline->id, 'title' => 'Título atualizado']);
    }

    public function test_admin_can_delete_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('admin.disciplines.destroy', $discipline));
        $response->assertRedirect(route('admin.disciplines.index'));
        $this->assertDatabaseMissing('disciplines', ['id' => $discipline->id]);
    }

    public function test_discipline_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.disciplines.store'), [
            'title' => '',
            'course_id' => 99999,
            'teacher_id' => 99999,
        ]);
        $response->assertSessionHasErrors(['title', 'course_id', 'teacher_id']);
    }

    public function test_student_cannot_access_disciplines_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.disciplines.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_disciplines_index(): void
    {
        $response = $this->get(route('admin.disciplines.index'));
        $response->assertRedirect(route('login'));
    }
}
