<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\AreaRepository;
use App\Repositories\CourseRepository;
use App\Repositories\StudentRepository;
use Carbon\Carbon;

class StudentChartDataService
{
    public function __construct(
        private CourseRepository $courseRepository,
        private StudentRepository $studentRepository,
        private AreaRepository $areaRepository
    ) {}

    /**
     * Admin dashboard: one chart per student-related metric.
     * Returns data formatted for Chart.js (labels + data arrays).
     */
    public function adminChartData(): array
    {
        return [
            'summary' => $this->summary(),
            'students_per_course' => $this->studentsPerCourse(),
            'average_age_per_course' => $this->averageAgePerCourse(),
            'students_by_age_range' => $this->studentsByAgeRange(),
            'enrollments_per_course' => $this->enrollmentsPerCourse(),
            'students_per_area' => $this->studentsPerArea(),
            'enrollments_per_month' => $this->enrollmentsPerMonth(),
            'students_per_month' => $this->studentsPerMonth(),
            'disciplines_per_course' => $this->disciplinesPerCourse(),
        ];
    }

    private function summary(): array
    {
        return [
            'students' => Student::count(),
            'enrollments' => Enrollment::count(),
            'courses' => Course::count(),
            'teachers' => Teacher::count(),
            'areas' => Area::count(),
            'disciplines' => Discipline::count(),
        ];
    }

    private function enrollmentsPerMonth(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }
        $labels = $months->map(fn ($d) => $this->formatMonthLabel($d))->toArray();
        $data = $months->map(function ($date) {
            return Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();
        return ['labels' => $labels, 'data' => $data];
    }

    private function studentsPerMonth(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }
        $labels = $months->map(fn ($d) => $this->formatMonthLabel($d))->toArray();
        $data = $months->map(function ($date) {
            return Student::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();
        return ['labels' => $labels, 'data' => $data];
    }

    private function formatMonthLabel(Carbon $date): string
    {
        $mes = mb_substr($date->locale('pt_BR')->translatedFormat('M'), 0, 3);
        $mes = preg_replace('/\W/u', '', $mes);
        $ano = $date->format('y');

        return $mes !== '' && $ano !== '' ? "{$mes} {$ano}" : $date->format('m/y');
    }

    private function disciplinesPerCourse(): array
    {
        $courses = Course::withCount('disciplines')->orderByDesc('disciplines_count')->limit(10)->get();
        return [
            'labels' => $courses->pluck('title')->toArray(),
            'data' => $courses->pluck('disciplines_count')->toArray(),
        ];
    }

    /**
     * Student dashboard: one chart per user info (my courses, my age, my enrollments).
     * Returns data formatted for Chart.js.
     */
    public function studentChartData(User $user): array
    {
        $student = $user->student;
        if (! $student) {
            return [
                'my_courses' => ['labels' => [], 'data' => []],
                'my_age' => ['value' => 0],
                'my_enrollments' => ['labels' => [], 'data' => []],
            ];
        }

        $courses = $student->courses;
        $age = $student->birth_date ? $student->birth_date->age : 0;

        return [
            'my_courses' => [
                'labels' => $courses->pluck('title')->toArray(),
                'data' => $courses->map(fn () => 1)->values()->toArray(),
            ],
            'my_age' => ['value' => $age],
            'my_enrollments' => [
                'labels' => $courses->pluck('title')->toArray(),
                'data' => $courses->pluck('title')->map(fn () => 1)->values()->toArray(),
            ],
        ];
    }

    private function studentsPerCourse(): array
    {
        $items = $this->courseRepository->allWithStudentsCount();
        return [
            'labels' => $items->pluck('title')->toArray(),
            'data' => $items->pluck('students_count')->toArray(),
        ];
    }

    private function averageAgePerCourse(): array
    {
        $courses = $this->courseRepository->allWithStudents();
        $labels = [];
        $data = [];
        foreach ($courses as $course) {
            $ages = $course->students->filter(fn ($s) => $s->birth_date)->map(fn ($s) => $s->birth_date->age);
            $labels[] = $course->title;
            $data[] = $ages->isEmpty() ? 0 : round($ages->avg(), 1);
        }
        return ['labels' => $labels, 'data' => $data];
    }

    private function studentsByAgeRange(): array
    {
        $students = $this->studentRepository->allWithBirthDate();
        $ranges = ['0-17' => 0, '18-25' => 0, '26-35' => 0, '36-50' => 0, '50+' => 0];
        foreach ($students as $s) {
            $age = $s->birth_date->age;
            if ($age <= 17) $ranges['0-17']++;
            elseif ($age <= 25) $ranges['18-25']++;
            elseif ($age <= 35) $ranges['26-35']++;
            elseif ($age <= 50) $ranges['36-50']++;
            else $ranges['50+']++;
        }
        return [
            'labels' => array_keys($ranges),
            'data' => array_values($ranges),
        ];
    }

    private function enrollmentsPerCourse(): array
    {
        $items = $this->courseRepository->allWithStudentsCount();
        return [
            'labels' => $items->pluck('title')->toArray(),
            'data' => $items->pluck('students_count')->toArray(),
        ];
    }

    private function studentsPerArea(): array
    {
        $areas = $this->areaRepository->allWithCoursesAndStudents();
        $labels = [];
        $data = [];
        foreach ($areas as $area) {
            $count = $area->courses->pluck('students')->flatten()->unique('id')->count();
            $labels[] = $area->name;
            $data[] = $count;
        }
        return ['labels' => $labels, 'data' => $data];
    }
}
