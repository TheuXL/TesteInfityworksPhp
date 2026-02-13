@extends('layouts.app')
@section('title', 'Editar Matrícula')
@section('content')
<h2 class="mb-4">Editar Matrícula</h2>
<form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
@csrf
@method('PUT')
<div class="mb-3">
<label for="student_id" class="form-label">Aluno *</label>
<select class="form-select" id="student_id" name="student_id" required>
@foreach($students as $s)
<option value="{{ $s->id }}" @if(old('student_id', $enrollment->student_id) == $s->id) selected @endif>{{ $s->name }}</option>
@endforeach
</select>
</div>
<div class="mb-3">
<label for="course_id" class="form-label">Curso *</label>
<select class="form-select" id="course_id" name="course_id" required>
@foreach($courses as $c)
<option value="{{ $c->id }}" @if(old('course_id', $enrollment->course_id) == $c->id) selected @endif>{{ $c->title }}</option>
@endforeach
</select>
</div>
<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
