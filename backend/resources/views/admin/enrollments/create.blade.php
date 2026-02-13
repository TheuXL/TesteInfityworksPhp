@extends('layouts.app')
@section('title', 'Nova Matrícula')
@section('content')
<h2 class="mb-4">Nova Matrícula</h2>
<form action="{{ route('admin.enrollments.store') }}" method="POST">
@csrf
<div class="mb-3">
<label for="student_id" class="form-label">Aluno *</label>
<select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
<option value="">Selecione</option>
@foreach($students as $s)
<option value="{{ $s->id }}" @selected(old('student_id') == $s->id)>{{ $s->name }} ({{ $s->email }})</option>
@endforeach
</select>
@error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
<label for="course_id" class="form-label">Curso *</label>
<select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
<option value="">Selecione</option>
@foreach($courses as $c)
<option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>{{ $c->title }}</option>
@endforeach
</select>
@error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
