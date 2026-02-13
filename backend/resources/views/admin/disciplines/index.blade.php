@extends('layouts.app')

@section('title', 'Disciplinas')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2>Disciplinas</h2>
    <a href="{{ route('admin.disciplines.create') }}" class="btn btn-primary">Nova disciplina</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Título</th><th>Curso</th><th>Professor</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            @forelse($disciplines as $discipline)
            <tr>
                <td>{{ $discipline->title }}</td>
                <td>{{ $discipline->course->title ?? '-' }}</td>
                <td>{{ $discipline->teacher->name ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.disciplines.show', $discipline) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    <a href="{{ route('admin.disciplines.edit', $discipline) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="{{ route('admin.disciplines.destroy', $discipline) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta disciplina?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">Nenhuma disciplina cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $disciplines->links() }}
@endsection
