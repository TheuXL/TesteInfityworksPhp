@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2>Cursos</h2>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Novo curso</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Título</th><th>Área</th><th>Início</th><th>Fim</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
            <tr>
                <td>{{ $course->title }}</td>
                <td>{{ $course->area->name ?? '-' }}</td>
                <td>{{ $course->start_date?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $course->end_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este curso?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">Nenhum curso cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $courses->links() }}
@endsection
