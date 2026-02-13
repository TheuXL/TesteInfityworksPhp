@extends('layouts.app')
@section('title', 'Editar Disciplina')
@section('content')
<h2 class="mb-4">Editar Disciplina</h2>
<form action="{{ route('admin.disciplines.update', $discipline) }}" method="POST">
@csrf
@method('PUT')
<div class="mb-3">
<label for="title" class="form-label">Título *</label>
<input type="text" class="form-control" id="title" name="title" value="{{ old('title', $discipline->title) }}" required>
</div>
<div class="mb-3">
<label for="description" class="form-label">Descrição</label>
<textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $discipline->description) }}</textarea>
</div>
<div class="mb-3">
<label for="course_id" class="form-label">Curso *</label>
<select class="form-select" id="course_id" name="course_id" required>
@foreach($courses as $c)
<option value="{{ $c->id }}" @if(old('course_id', $discipline->course_id) == $c->id) selected @endif>{{ $c->title }}</option>
@endforeach
</select>
</div>
<div class="mb-3">
<label for="teacher_id" class="form-label">Professor *</label>
<select class="form-select" id="teacher_id" name="teacher_id" required>
@foreach($teachers as $t)
<option value="{{ $t->id }}" @if(old('teacher_id', $discipline->teacher_id) == $t->id) selected @endif>{{ $t->name }}</option>
@endforeach
</select>
</div>
<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('admin.disciplines.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
