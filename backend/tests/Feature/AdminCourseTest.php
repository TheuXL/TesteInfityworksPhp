<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Cursos (admin): listar, criar, editar, excluir, validação e acesso. */
class AdminCourseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_see_courses_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.index');
    }

    public function test_admin_can_see_course_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.create');
    }

    public function test_admin_can_create_course(): void
    {
        $area = Area::factory()->create(['name' => 'Biologia']);
        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Curso de Biologia I',
            'description' => 'Descrição do curso',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'area_id' => $area->id,
        ]);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['title' => 'Curso de Biologia I', 'area_id' => $area->id]);
    }

    public function test_admin_can_see_course_show(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.courses.show', $course));
        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.show');
        $response->assertSee($course->title);
    }

    public function test_admin_can_see_course_edit_form(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('admin.courses.edit', $course));
        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.edit');
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create(['title' => 'Título antigo']);
        $area = Area::factory()->create();
        $response = $this->actingAs($this->admin)->put(route('admin.courses.update', $course), [
            'title' => 'Título atualizado',
            'description' => $course->description,
            'start_date' => $course->start_date->format('Y-m-d'),
            'end_date' => $course->end_date->format('Y-m-d'),
            'area_id' => $area->id,
        ]);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Título atualizado']);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('admin.courses.destroy', $course));
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_store_validates_required_title_and_area(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => '',
            'area_id' => 99999,
        ]);
        $response->assertSessionHasErrors(['title', 'area_id']);
    }

    public function test_student_cannot_access_courses_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('admin.courses.index'));
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_courses_index(): void
    {
        $response = $this->get(route('admin.courses.index'));
        $response->assertRedirect(route('login'));
    }
}
