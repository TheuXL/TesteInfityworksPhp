<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EnrollmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Enrollment::query()->with('student', 'course');

        switch ($sort) {
            case 'oldest':
                $query->orderBy('enrollments.created_at')->orderBy('enrollments.id');
                break;
            case 'name_asc':
                $query->join('students', 'enrollments.student_id', '=', 'students.id')
                    ->orderBy('students.name')->orderBy('enrollments.id')
                    ->select('enrollments.*');
                break;
            case 'name_desc':
                $query->join('students', 'enrollments.student_id', '=', 'students.id')
                    ->orderByDesc('students.name')->orderByDesc('enrollments.id')
                    ->select('enrollments.*');
                break;
            default:
                $query->orderByDesc('enrollments.created_at')->orderByDesc('enrollments.id');
        }

        $enrollments = $query->paginate($perPage)->withQueryString();
        return EnrollmentResource::collection($enrollments);
    }

    public function create(): JsonResponse
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::with('area')->orderBy('title')->get();
        return response()->json([
            'students' => StudentResource::collection($students),
            'courses' => CourseResource::collection($courses),
        ]);
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $enrollment = Enrollment::create($request->validated());
        return response()->json(new EnrollmentResource($enrollment->load(['student', 'course'])), 201);
    }

    public function show(Enrollment $enrollment): EnrollmentResource
    {
        return new EnrollmentResource($enrollment->load('student', 'course'));
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): EnrollmentResource
    {
        $enrollment->update($request->validated());
        return new EnrollmentResource($enrollment->load(['student', 'course']));
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();
        return response()->json(null, 204);
    }
}
