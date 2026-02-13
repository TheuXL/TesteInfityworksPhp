<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository
{
    public function allWithStudentsWithBirthDate(): Collection
    {
        return Course::with(['students' => fn ($q) => $q->whereNotNull('birth_date')])->get();
    }

    public function allWithStudentsCount(): Collection
    {
        return Course::withCount('students')->get();
    }

    public function allWithStudents(): Collection
    {
        return Course::with('students')->get();
    }
}
