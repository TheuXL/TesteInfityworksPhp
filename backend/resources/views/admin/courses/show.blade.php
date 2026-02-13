@extends('layouts.app')

@section('title', $course->title)

@section('content')
<h2 class="mb-4">{{ $course->title }}</h2>
<dl class="row">
    <dt class="col-sm-2">Área</dt>
    <dd class="col-sm-10">{{ $course->area->name ?? '-' }}</dd>
    <dt class="col-sm-2">Descrição</dt>
    <dd class="col-sm-10">{{ $course->description ?: '-' }}</dd>
    <dt class="col-sm-2">Início</dt>
    <dd class="col-sm-10">{{ $course->start_date?->format('d/m/Y') ?? '-' }}</dd>
    <dt class="col-sm-2">Fim</dt>
    <dd class="col-sm-10">{{ $course->end_date?->format('d/m/Y') ?? '-' }}</dd>
</dl>
<a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
