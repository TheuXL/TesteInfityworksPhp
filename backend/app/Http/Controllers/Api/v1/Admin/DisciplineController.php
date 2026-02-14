<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDisciplineRequest;
use App\Http\Requests\UpdateDisciplineRequest;
use App\Http\Resources\DisciplineResource;
use App\Models\Discipline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DisciplineController extends Controller
{
    public function index(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Discipline::query()->with(['course', 'teacher']);

        match ($sort) {
            'oldest' => $query->orderBy('disciplines.created_at')->orderBy('disciplines.id'),
            'name_asc' => $query->orderBy('title')->orderBy('id'),
            'name_desc' => $query->orderByDesc('title')->orderByDesc('id'),
            default => $query->orderByDesc('disciplines.created_at')->orderByDesc('disciplines.id'),
        };

        $disciplines = $query->paginate($perPage)->withQueryString();
        return DisciplineResource::collection($disciplines);
    }

    public function store(StoreDisciplineRequest $request): JsonResponse
    {
        $discipline = Discipline::create($request->validated());
        return response()->json(new DisciplineResource($discipline->load(['course', 'teacher'])), 201);
    }

    public function show(Discipline $discipline): DisciplineResource
    {
        return new DisciplineResource($discipline->load(['course', 'teacher']));
    }

    public function update(UpdateDisciplineRequest $request, Discipline $discipline): DisciplineResource
    {
        $discipline->update($request->validated());
        return new DisciplineResource($discipline->load(['course', 'teacher']));
    }

    public function destroy(Discipline $discipline): JsonResponse
    {
        $discipline->delete();
        return response()->json(null, 204);
    }
}
