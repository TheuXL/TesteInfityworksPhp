@extends('layouts.app')
@section('title', 'Matrículas')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
<h2>Matrículas</h2>
<a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">Nova matrícula</a>
</div>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr><th>Aluno</th><th>Curso</th><th class="text-end">Ações</th></tr>
</thead>
<tbody>
@forelse($enrollments as $e)
<tr>
<td>{{ $e->student->name }}</td>
<td>{{ $e->course->title }}</td>
<td class="text-end">
<a href="{{ route('admin.enrollments.show', $e) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
<a href="{{ route('admin.enrollments.edit', $e) }}" class="btn btn-sm btn-outline-primary">Editar</a>
<form action="{{ route('admin.enrollments.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?');">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="3" class="text-center text-muted">Nenhuma matrícula.</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $enrollments->links() }}
@endsection
