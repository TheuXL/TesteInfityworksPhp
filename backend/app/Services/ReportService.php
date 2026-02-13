<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    /**
     * Returns per course: average age, youngest student, oldest student.
     * Uses Carbon for age calculation and collection aggregations (avg, min, max).
     *
     * @return Collection<int, array{course: Course, average_age: float, youngest: array|null, oldest: array|null}>
     */
    public function courseAgesReport(): Collection
    {
        $courses = $this->courseRepository->allWithStudentsWithBirthDate();

        return $courses->map(function (Course $course) {
            $students = $course->students->filter(fn ($s) => $s->birth_date !== null);
            $ages = $students->map(fn ($s) => Carbon::parse($s->birth_date)->age)->values();

            $averageAge = $ages->isEmpty() ? 0.0 : round((float) $ages->avg(), 1);
            $minAge = $ages->isEmpty() ? null : $ages->min();
            $maxAge = $ages->isEmpty() ? null : $ages->max();

            $youngest = $ages->isEmpty() ? null : $students->first(fn ($s) => Carbon::parse($s->birth_date)->age === $minAge);
            $oldest = $ages->isEmpty() ? null : $students->first(fn ($s) => Carbon::parse($s->birth_date)->age === $maxAge);

            return [
                'course' => $course,
                'average_age' => $averageAge,
                'youngest' => $youngest ? ['id' => $youngest->id, 'name' => $youngest->name, 'age' => $youngest->birth_date->age] : null,
                'oldest' => $oldest ? ['id' => $oldest->id, 'name' => $oldest->name, 'age' => $oldest->birth_date->age] : null,
            ];
        });
    }
}
