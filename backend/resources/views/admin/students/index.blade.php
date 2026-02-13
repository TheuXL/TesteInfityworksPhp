@extends('layouts.app')
@section('title', 'Alunos')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
<h2>Alunos</h2>
<a href="{{ route('admin.students.create') }}" class="btn btn-primary">Novo aluno</a>
</div>
<form action="{{ route('admin.students.index') }}" method="GET" class="mb-4">
<div class="row g-2">
<div class="col-12 col-md-6 col-lg-4">
<input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou e-mail">
</div>
<div class="col-auto">
<button type="submit" class="btn btn-secondary">Filtrar</button>
@if(request('search'))
<a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Limpar</a>
@endif
</div>
</div>
</form>
<div class="table-responsive">
<table class="table table-striped table-hover">
<thead>
<tr><th>Nome</th><th>E-mail</th><th>Nascimento</th><th class="text-end">Ações</th></tr>
</thead>
<tbody>
@forelse($students as $student)
<tr>
<td>{{ $student->name }}</td>
<td>{{ $student->email }}</td>
<td>{{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}</td>
<td class="text-end">
<a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Editar</a>
<form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?');">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted">Nenhum aluno.</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $students->links() }}
@endsection
