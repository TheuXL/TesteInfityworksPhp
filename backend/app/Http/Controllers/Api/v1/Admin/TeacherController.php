<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherController extends Controller
{
    public function index(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Teacher::query();

        match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'name_asc' => $query->orderBy('name')->orderBy('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        $teachers = $query->paginate($perPage)->withQueryString();
        return TeacherResource::collection($teachers);
    }

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $teacher = Teacher::create($request->validated());
        return response()->json(new TeacherResource($teacher), 201);
    }

    public function show(Teacher $teacher): TeacherResource
    {
        return new TeacherResource($teacher);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): TeacherResource
    {
        $teacher->update($request->validated());
        return new TeacherResource($teacher);
    }

    public function destroy(Teacher $teacher): JsonResponse
    {
        $teacher->delete();
        return response()->json(null, 204);
    }
}
