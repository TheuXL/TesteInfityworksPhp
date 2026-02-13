<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'course_id' => ['required', 'exists:courses,id', Rule::unique('enrollments', 'course_id')->where('student_id', $this->input('student_id'))],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.unique' => 'Este aluno já está matriculado neste curso.',
        ];
    }
}
