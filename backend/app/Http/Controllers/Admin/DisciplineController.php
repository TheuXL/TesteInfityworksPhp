<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDisciplineRequest;
use App\Http\Requests\UpdateDisciplineRequest;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Teacher;

class DisciplineController extends Controller
{
    public function index()
    {
        $disciplines = Discipline::with('course', 'teacher')->orderBy('title')->paginate(10);
        return view('admin.disciplines.index', compact('disciplines'));
    }

    public function create()
    {
        $courses = Course::with('area')->orderBy('title')->get();
        $teachers = Teacher::orderBy('name')->get();
        return view('admin.disciplines.create', compact('courses', 'teachers'));
    }

    public function store(StoreDisciplineRequest $request)
    {
        Discipline::create($request->validated());
        return redirect()->route('admin.disciplines.index')->with('success', 'Disciplina criada com sucesso.');
    }

    public function show(Discipline $discipline)
    {
        $discipline->load('course', 'teacher');
        return view('admin.disciplines.show', compact('discipline'));
    }

    public function edit(Discipline $discipline)
    {
        $courses = Course::orderBy('title')->get();
        $teachers = Teacher::orderBy('name')->get();
        return view('admin.disciplines.edit', compact('discipline', 'courses', 'teachers'));
    }

    public function update(UpdateDisciplineRequest $request, Discipline $discipline)
    {
        $discipline->update($request->validated());
        return redirect()->route('admin.disciplines.index')->with('success', 'Disciplina atualizada com sucesso.');
    }

    public function destroy(Discipline $discipline)
    {
        $discipline->delete();
        return redirect()->route('admin.disciplines.index')->with('success', 'Disciplina excluída com sucesso.');
    }
}
