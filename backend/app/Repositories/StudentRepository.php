<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository
{
    public function allWithBirthDate(): Collection
    {
        return Student::whereNotNull('birth_date')->get();
    }
}
