@extends('layouts.app')

@section('title', 'Áreas')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2>Áreas de Curso</h2>
    <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">Nova área</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Nome</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            @forelse($areas as $area)
            <tr>
                <td>{{ $area->name }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.areas.show', $area) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta área?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="2" class="text-center text-muted">Nenhuma área cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $areas->links() }}
@endsection
