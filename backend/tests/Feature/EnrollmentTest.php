<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Testes de Matrículas (admin): criar, listar, editar, excluir, duplicidade e acesso. */
class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.enrollments.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));
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

        $this->actingAs($admin)->post(route('admin.enrollments.store'), [
            'student_id' => $student->id,
            'course_id' => $course1->id,
        ]);
        $this->actingAs($admin)->post(route('admin.enrollments.store'), [
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
        $response = $this->actingAs($student)->get(route('admin.enrollments.create'));
        $response->assertStatus(403);
    }

    public function test_duplicate_enrollment_same_student_same_course_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id]);

        $response = $this->actingAs($admin)->post(route('admin.enrollments.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $response->assertSessionHasErrors('course_id');
        $this->assertCount(1, Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->get());
    }

    public function test_admin_can_see_enrollments_index(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.enrollments.index');
    }

    public function test_admin_can_see_enrollment_show(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $response = $this->actingAs($admin)->get(route('admin.enrollments.show', $enrollment));
        $response->assertStatus(200);
        $response->assertViewIs('admin.enrollments.show');
    }

    public function test_admin_can_update_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $otherCourse = Course::factory()->create();
        $response = $this->actingAs($admin)->put(route('admin.enrollments.update', $enrollment), [
            'student_id' => $enrollment->student_id,
            'course_id' => $otherCourse->id,
        ]);
        $response->assertRedirect(route('admin.enrollments.index'));
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id, 'course_id' => $otherCourse->id]);
    }

    public function test_admin_can_delete_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $response = $this->actingAs($admin)->delete(route('admin.enrollments.destroy', $enrollment));
        $response->assertRedirect(route('admin.enrollments.index'));
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_guest_cannot_access_enrollments_index(): void
    {
        $response = $this->get(route('admin.enrollments.index'));
        $response->assertRedirect(route('login'));
    }
}
