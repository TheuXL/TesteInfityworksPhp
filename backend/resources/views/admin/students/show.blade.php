@extends('layouts.app')

@section('title', $student->name)

@section('content')
<h2 class="mb-4">{{ $student->name }}</h2>
<dl class="row">
    <dt class="col-sm-2">E-mail</dt>
    <dd class="col-sm-10">{{ $student->email }}</dd>
    <dt class="col-sm-2">Data de nascimento</dt>
    <dd class="col-sm-10">{{ $student->birth_date?->format('d/m/Y') ?? '-' }}</dd>
    <dt class="col-sm-2">Cursos matriculados</dt>
    <dd class="col-sm-10">
        @forelse($student->courses as $c)
        <span class="badge bg-secondary">{{ $c->title }}</span>
        @empty
        -
        @endforelse
    </dd>
</dl>
<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
