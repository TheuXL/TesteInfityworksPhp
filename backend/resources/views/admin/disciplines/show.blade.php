@extends('layouts.app')
@section('title', $discipline->title)
@section('content')
<h2 class="mb-4">{{ $discipline->title }}</h2>
<dl class="row">
<dt class="col-sm-2">Curso</dt>
<dd class="col-sm-10">{{ $discipline->course->title ?? '-' }}</dd>
<dt class="col-sm-2">Professor</dt>
<dd class="col-sm-10">{{ $discipline->teacher->name ?? '-' }}</dd>
<dt class="col-sm-2">Descrição</dt>
<dd class="col-sm-10">{{ $discipline->description ?: '-' }}</dd>
</dl>
<a href="{{ route('admin.disciplines.edit', $discipline) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.disciplines.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
