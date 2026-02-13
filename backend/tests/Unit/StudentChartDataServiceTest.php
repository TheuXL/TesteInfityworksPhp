<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use App\Repositories\AreaRepository;
use App\Repositories\CourseRepository;
use App\Repositories\StudentRepository;
use App\Services\StudentChartDataService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

/** Testes do StudentChartDataService: dados para gráficos (admin e aluno). */
class StudentChartDataServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_admin_chart_data_returns_structure_for_chart_js(): void
    {
        $courseRepo = Mockery::mock(CourseRepository::class);
        $courseRepo->shouldReceive('allWithStudentsCount')->andReturn(new EloquentCollection([
            (object) ['title' => 'Curso A', 'students_count' => 5],
        ]));
        $courseRepo->shouldReceive('allWithStudents')->andReturn(new EloquentCollection([]));
        $studentRepo = Mockery::mock(StudentRepository::class);
        $studentRepo->shouldReceive('allWithBirthDate')->andReturn(new EloquentCollection([]));
        $areaRepo = Mockery::mock(AreaRepository::class);
        $areaRepo->shouldReceive('allWithCoursesAndStudents')->andReturn(new EloquentCollection([]));

        $service = new StudentChartDataService($courseRepo, $studentRepo, $areaRepo);
        $data = $service->adminChartData();

        $this->assertArrayHasKey('students_per_course', $data);
        $this->assertArrayHasKey('labels', $data['students_per_course']);
        $this->assertArrayHasKey('data', $data['students_per_course']);
        $this->assertArrayHasKey('average_age_per_course', $data);
        $this->assertArrayHasKey('students_by_age_range', $data);
        $this->assertArrayHasKey('enrollments_per_course', $data);
        $this->assertArrayHasKey('students_per_area', $data);
    }

    public function test_student_chart_data_returns_empty_structure_when_user_has_no_student(): void
    {
        $user = new User;
        $user->setRelation('student', null);

        $courseRepo = Mockery::mock(CourseRepository::class);
        $studentRepo = Mockery::mock(StudentRepository::class);
        $areaRepo = Mockery::mock(AreaRepository::class);

        $service = new StudentChartDataService($courseRepo, $studentRepo, $areaRepo);
        $data = $service->studentChartData($user);

        $this->assertEquals(['labels' => [], 'data' => []], $data['my_courses']);
        $this->assertEquals(['value' => 0], $data['my_age']);
        $this->assertEquals(['labels' => [], 'data' => []], $data['my_enrollments']);
    }

    public function test_student_chart_data_returns_labels_and_data_when_student_has_courses(): void
    {
        $student = new Student;
        $student->birth_date = now()->subYears(22);
        $student->setRelation('courses', collect([
            (object) ['title' => 'Curso 1'],
            (object) ['title' => 'Curso 2'],
        ]));

        $user = new User;
        $user->setRelation('student', $student);

        $courseRepo = Mockery::mock(CourseRepository::class);
        $studentRepo = Mockery::mock(StudentRepository::class);
        $areaRepo = Mockery::mock(AreaRepository::class);

        $service = new StudentChartDataService($courseRepo, $studentRepo, $areaRepo);
        $data = $service->studentChartData($user);

        $this->assertEquals(['Curso 1', 'Curso 2'], $data['my_courses']['labels']);
        $this->assertEquals(22, $data['my_age']['value']);
    }
}
