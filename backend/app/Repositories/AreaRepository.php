<?php

namespace App\Repositories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;

class AreaRepository
{
    public function allWithCoursesAndStudents(): Collection
    {
        return Area::with('courses.students')->get();
    }
}
