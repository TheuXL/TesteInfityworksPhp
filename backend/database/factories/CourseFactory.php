<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 year', '+1 month');
        $end = clone $start;
        $end->modify('+6 months');
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $start,
            'end_date' => $end,
            'area_id' => Area::factory(),
        ];
    }
}
