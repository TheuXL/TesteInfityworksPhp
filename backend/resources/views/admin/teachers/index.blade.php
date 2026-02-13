@extends('layouts.app')

@section('title', 'Professores')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2>Professores</h2>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Novo professor</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Nome</th><th>E-mail</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            @forelse($teachers as $teacher)
            <tr>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->email ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este professor?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted">Nenhum professor cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $teachers->links() }}
@endsection
