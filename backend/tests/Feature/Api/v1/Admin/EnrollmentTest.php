<?php

namespace Tests\Feature\Api\v1\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes de Matrículas (admin) via API v1. */
class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_one_student_can_be_enrolled_in_multiple_courses(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();

        $this->actingAs($admin)->postJson('/api/v1/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course1->id,
        ]);
        $this->actingAs($admin)->postJson('/api/v1/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course2->id,
        ]);

        $this->assertCount(2, Enrollment::where('student_id', $student->id)->get());
        $this->assertTrue($student->fresh()->courses->pluck('id')->contains($course1->id));
        $this->assertTrue($student->fresh()->courses->pluck('id')->contains($course2->id));
    }

    public function test_student_cannot_access_enrollment_create(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get('/api/v1/admin/enrollments/create');
        $response->assertStatus(403);
    }

    public function test_duplicate_enrollment_same_student_same_course_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id]);

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('course_id');
        $this->assertCount(1, Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->get());
    }

    public function test_admin_can_see_enrollments_index(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->getJson('/api/v1/admin/enrollments');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_see_enrollment_show(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $response = $this->actingAs($admin)->getJson('/api/v1/admin/enrollments/' . $enrollment->id);
        $response->assertStatus(200);
    }

    public function test_admin_can_update_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $otherCourse = Course::factory()->create();
        $response = $this->actingAs($admin)->putJson('/api/v1/admin/enrollments/' . $enrollment->id, [
            'student_id' => $enrollment->student_id,
            'course_id' => $otherCourse->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id, 'course_id' => $otherCourse->id]);
    }

    public function test_admin_can_delete_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $response = $this->actingAs($admin)->deleteJson('/api/v1/admin/enrollments/' . $enrollment->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_guest_cannot_access_enrollments_index(): void
    {
        $response = $this->getJson('/api/v1/admin/enrollments');
        $response->assertStatus(401);
    }
}
