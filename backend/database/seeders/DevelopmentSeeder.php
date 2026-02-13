<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotente: se já foi seedado (ex.: container reiniciou com volume MySQL persistido), não duplicar
        if (User::where('email', 'admin@plataforma.test')->exists()) {
            $this->command->info('Database already seeded (admin exists). Skipping.');
            return;
        }

        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@plataforma.test',
            'password' => Hash::make('password'),
        ]);

        $areas = collect([
            Area::create(['name' => 'Biologia']),
            Area::create(['name' => 'Física']),
            Area::create(['name' => 'Química']),
            Area::create(['name' => 'Matemática']),
        ]);

        $courses = collect();
        foreach ($areas as $area) {
            $courses->push(
                Course::create([
                    'title' => 'Curso de ' . $area->name . ' I',
                    'description' => 'Descrição do curso de ' . $area->name,
                    'start_date' => now()->subMonths(2),
                    'end_date' => now()->addMonths(4),
                    'area_id' => $area->id,
                ])
            );
            $courses->push(
                Course::create([
                    'title' => 'Curso de ' . $area->name . ' II',
                    'description' => null,
                    'start_date' => now()->subMonth(),
                    'end_date' => now()->addMonths(5),
                    'area_id' => $area->id,
                ])
            );
        }

        $teachers = collect();
        for ($i = 0; $i < 8; $i++) {
            $teachers->push(Teacher::create([
                'name' => 'Professor ' . fake()->firstName() . ' ' . fake()->lastName(),
                'email' => 'prof' . ($i + 1) . '@plataforma.test',
            ]));
        }

        $courseList = $courses->all();
        $teacherIndex = 0;
        foreach ($courseList as $course) {
            Discipline::create([
                'title' => 'Disciplina ' . $course->title,
                'description' => null,
                'course_id' => $course->id,
                'teacher_id' => $teachers[$teacherIndex % $teachers->count()]->id,
            ]);
            $teacherIndex++;
        }

        $students = collect();
        $birthDates = [
            now()->subYears(18),
            now()->subYears(22),
            now()->subYears(25),
            now()->subYears(30),
            now()->subYears(35),
            now()->subYears(40),
            now()->subYears(45),
            now()->subYears(50),
            now()->subYears(20),
            now()->subYears(28),
        ];
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()->student()->create([
                'password' => Hash::make('password'),
            ]);
            $students->push(Student::create([
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $birthDates[$i % count($birthDates)],
                'user_id' => $user->id,
            ]));
        }

        $studentEmanuel = $students->first();
        User::where('id', $studentEmanuel->user_id)->update(['name' => 'Emanuel', 'email' => 'emanuel@plataforma.test']);
        $studentEmanuel->update(['name' => 'Emanuel', 'email' => 'emanuel@plataforma.test']);

        foreach ($students as $index => $student) {
            $numEnrollments = rand(1, min(4, $courses->count()));
            $enrolled = $courses->random($numEnrollments);
            foreach ($enrolled as $course) {
                Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'course_id' => $course->id],
                    []
                );
            }
        }

        $this->command->info('Admin: admin@plataforma.test / password');
        $this->command->info('Aluno (Emanuel): emanuel@plataforma.test / password');
    }
}
