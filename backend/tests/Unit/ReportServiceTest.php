<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Student;
use App\Repositories\CourseRepository;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

/** Testes do ReportService: relatório de idades por curso (média, mais novo, mais velho). */
class ReportServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_course_ages_report_returns_collection_with_average_youngest_oldest(): void
    {
        $student1 = new Student;
        $student1->id = 1;
        $student1->name = 'Aluno A';
        $student1->birth_date = now()->subYears(20);

        $student2 = new Student;
        $student2->id = 2;
        $student2->name = 'Aluno B';
        $student2->birth_date = now()->subYears(30);

        $course = new Course;
        $course->id = 1;
        $course->title = 'Curso Teste';
        $course->students = collect([$student1, $student2]);

        $repository = Mockery::mock(CourseRepository::class);
        $repository->shouldReceive('allWithStudentsWithBirthDate')
            ->once()
            ->andReturn(new EloquentCollection([$course]));

        $service = new ReportService($repository);
        $report = $service->courseAgesReport();

        $this->assertInstanceOf(Collection::class, $report);
        $this->assertCount(1, $report);
        $row = $report->first();
        $this->assertSame($course, $row['course']);
        $this->assertEquals(25.0, $row['average_age']);
        $this->assertNotNull($row['youngest']);
        $this->assertEquals(20, $row['youngest']['age']);
        $this->assertNotNull($row['oldest']);
        $this->assertEquals(30, $row['oldest']['age']);
    }

    public function test_course_ages_report_returns_zero_average_when_no_students_with_birth_date(): void
    {
        $course = new Course;
        $course->id = 1;
        $course->title = 'Curso Vazio';
        $course->students = collect([]);

        $repository = Mockery::mock(CourseRepository::class);
        $repository->shouldReceive('allWithStudentsWithBirthDate')
            ->once()
            ->andReturn(new EloquentCollection([$course]));

        $service = new ReportService($repository);
        $report = $service->courseAgesReport();

        $row = $report->first();
        $this->assertEquals(0.0, $row['average_age']);
        $this->assertNull($row['youngest']);
        $this->assertNull($row['oldest']);
    }
}
