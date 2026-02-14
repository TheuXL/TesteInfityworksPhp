<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Student::query()->search($request->input('search'));

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at')->orderBy('id');
                break;
            case 'name_asc':
                $query->orderBy('name')->orderBy('id');
                break;
            case 'name_desc':
                $query->orderByDesc('name')->orderByDesc('id');
                break;
            default:
                $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $students = $query->paginate($perPage)->withQueryString();
        return StudentResource::collection($students);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());
        return response()->json(new StudentResource($student), 201);
    }

    public function show(Student $student): StudentResource
    {
        return new StudentResource($student->load('courses'));
    }

    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        $student->update($request->validated());
        return new StudentResource($student);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();
        return response()->json(null, 204);
    }
}
