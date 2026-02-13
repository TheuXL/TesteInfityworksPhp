@extends('layouts.app')
@section('title', 'Matrícula')
@section('content')
<h2 class="mb-4">Matrícula</h2>
<dl class="row">
<dt class="col-sm-2">Aluno</dt>
<dd class="col-sm-10">{{ $enrollment->student->name }}</dd>
<dt class="col-sm-2">Curso</dt>
<dd class="col-sm-10">{{ $enrollment->course->title }}</dd>
</dl>
<a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
