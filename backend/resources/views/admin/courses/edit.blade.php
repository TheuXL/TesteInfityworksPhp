@extends('layouts.app')
@section('title', 'Editar Curso')
@section('content')
<h2 class="mb-4">Editar Curso</h2>
<form action="{{ route('admin.courses.update', $course) }}" method="POST">
@csrf
@method('PUT')
<div class="mb-3">
<label for="title" class="form-label">Título *</label>
<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $course->title) }}" required>
@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
<label for="description" class="form-label">Descrição</label>
<textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $course->description) }}</textarea>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label for="start_date" class="form-label">Data de início</label>
<input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
</div>
<div class="col-md-6 mb-3">
<label for="end_date" class="form-label">Data de fim</label>
<input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
</div>
</div>
<div class="mb-3">
<label for="area_id" class="form-label">Área *</label>
<select class="form-select" id="area_id" name="area_id" required>
@foreach($areas as $a)
<option value="{{ $a->id }}" @selected(old('area_id', $course->area_id) == $a->id)>{{ $a->name }}</option>
@endforeach
</select>
</div>
<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
