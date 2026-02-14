<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AreaController extends Controller
{
    public function index(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $sort = $request->input('sort', 'newest');
        $perPage = min((int) $request->input('per_page', 5000), 10000);

        $query = Area::query();

        match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'name_asc' => $query->orderBy('name')->orderBy('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        $areas = $query->paginate($perPage)->withQueryString();
        return AreaResource::collection($areas);
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        $area = Area::create($request->validated());
        return response()->json(new AreaResource($area), 201);
    }

    public function show(Area $area): AreaResource
    {
        return new AreaResource($area);
    }

    public function update(UpdateAreaRequest $request, Area $area): AreaResource
    {
        $area->update($request->validated());
        return new AreaResource($area);
    }

    public function destroy(Area $area): JsonResponse
    {
        $area->delete();
        return response()->json(null, 204);
    }
}
