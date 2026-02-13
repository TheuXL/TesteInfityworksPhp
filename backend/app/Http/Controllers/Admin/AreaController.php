<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('name')->paginate(10);
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.create');
    }

    public function store(StoreAreaRequest $request)
    {
        Area::create($request->validated());
        return redirect()->route('admin.areas.index')->with('success', 'Área criada com sucesso.');
    }

    public function show(Area $area)
    {
        return view('admin.areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(UpdateAreaRequest $request, Area $area)
    {
        $area->update($request->validated());
        return redirect()->route('admin.areas.index')->with('success', 'Área atualizada com sucesso.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Área excluída com sucesso.');
    }
}
