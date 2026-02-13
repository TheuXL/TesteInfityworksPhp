@extends('layouts.app')

@section('title', $teacher->name)

@section('content')
<h2 class="mb-4">{{ $teacher->name }}</h2>
<dl class="row">
    <dt class="col-sm-2">E-mail</dt>
    <dd class="col-sm-10">{{ $teacher->email ?? '-' }}</dd>
</dl>
<a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
