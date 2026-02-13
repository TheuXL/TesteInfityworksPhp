<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Area;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('area')->orderBy('title')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $areas = Area::orderBy('name')->get();
        return view('admin.courses.create', compact('areas'));
    }

    public function store(StoreCourseRequest $request)
    {
        Course::create($request->validated());
        return redirect()->route('admin.courses.index')->with('success', 'Curso criado com sucesso.');
    }

    public function show(Course $course)
    {
        $course->load('area', 'disciplines.teacher', 'students');
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $areas = Area::orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'areas'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        return redirect()->route('admin.courses.index')->with('success', 'Curso atualizado com sucesso.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Curso excluído com sucesso.');
    }
}
