<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Discipline;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisciplineFactory extends Factory
{
    protected $model = Discipline::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
            'description' => fake()->optional()->paragraph(),
            'course_id' => Course::factory(),
            'teacher_id' => Teacher::factory(),
        ];
    }
}
