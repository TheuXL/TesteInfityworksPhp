<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Area;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes do CRUD de Cursos (admin) via API v1. */
class CourseTest extends TestCase
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
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/courses');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_see_course_create_form(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/areas');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_course(): void
    {
        $area = Area::factory()->create(['name' => 'Biologia']);
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/courses', [
            'title' => 'Curso de Biologia I',
            'description' => 'Descrição do curso',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'area_id' => $area->id,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', ['title' => 'Curso de Biologia I', 'area_id' => $area->id]);
    }

    public function test_admin_can_see_course_show(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/courses/' . $course->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $course->title]);
    }

    public function test_admin_can_see_course_edit_form(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/courses/' . $course->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create(['title' => 'Título antigo']);
        $area = Area::factory()->create();
        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/courses/' . $course->id, [
            'title' => 'Título atualizado',
            'description' => $course->description,
            'start_date' => $course->start_date->format('Y-m-d'),
            'end_date' => $course->end_date->format('Y-m-d'),
            'area_id' => $area->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Título atualizado']);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/courses/' . $course->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_store_validates_required_title_and_area(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/courses', [
            'title' => '',
            'area_id' => 99999,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'area_id']);
    }

    public function test_student_cannot_access_courses_index(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/courses');
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_courses_index(): void
    {
        $response = $this->getJson('/api/v1/admin/courses');
        $response->assertStatus(401);
    }
}
