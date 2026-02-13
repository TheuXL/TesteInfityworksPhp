@extends('layouts.app')

@section('title', $area->name)

@section('content')
<h2 class="mb-4">{{ $area->name }}</h2>
<dl class="row">
    <dt class="col-sm-2">Nome</dt>
    <dd class="col-sm-10">{{ $area->name }}</dd>
</dl>
<a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-primary">Editar</a>
<a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
