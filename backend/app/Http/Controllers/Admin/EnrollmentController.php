<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with('student', 'course')->orderByDesc('created_at')->paginate(10);
        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::with('area')->orderBy('title')->get();
        return view('admin.enrollments.create', compact('students', 'courses'));
    }

    public function store(StoreEnrollmentRequest $request)
    {
        Enrollment::create($request->validated());
        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula realizada com sucesso.');
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load('student', 'course');
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());
        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula atualizada com sucesso.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula excluída com sucesso.');
    }
}
