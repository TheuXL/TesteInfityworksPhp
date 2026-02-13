@extends('layouts.app')

@section('title', 'Editar Área')

@section('content')
<h2 class="mb-4">Editar Área</h2>
<form action="{{ route('admin.areas.update', $area) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Nome *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $area->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
