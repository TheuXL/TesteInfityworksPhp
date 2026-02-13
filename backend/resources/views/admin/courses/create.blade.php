@extends('layouts.app')

@section('title', 'Novo Curso')

@section('content')
<h2 class="mb-4">Novo Curso</h2>
<form action="{{ route('admin.courses.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Título *</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descrição</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="start_date" class="form-label">Data de início</label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="end_date" class="form-label">Data de fim</label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="area_id" class="form-label">Área *</label>
        <select class="form-select @error('area_id') is-invalid @enderror" id="area_id" name="area_id" required>
            <option value="">Selecione</option>
            @foreach($areas as $a)
            <option value="{{ $a->id }}" @selected(old('area_id') == $a->id)>{{ $a->name }}</option>
            @endforeach
        </select>
        @error('area_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
