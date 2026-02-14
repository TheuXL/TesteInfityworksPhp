<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    public function index(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Course::query()->with('area');

        switch ($sort) {
            case 'oldest':
                $query->orderBy('courses.created_at')->orderBy('courses.id');
                break;
            case 'name_asc':
                $query->orderBy('title')->orderBy('id');
                break;
            case 'name_desc':
                $query->orderByDesc('title')->orderByDesc('id');
                break;
            default:
                $query->orderByDesc('courses.created_at')->orderByDesc('courses.id');
        }

        $courses = $query->paginate($perPage)->withQueryString();
        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = Course::create($request->validated());
        return response()->json(new CourseResource($course->load('area')), 201);
    }

    public function show(Course $course): CourseResource
    {
        return new CourseResource($course->load('area'));
    }

    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        $course->update($request->validated());
        return new CourseResource($course->load('area'));
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(null, 204);
    }
}
