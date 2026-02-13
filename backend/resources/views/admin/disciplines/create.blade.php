@extends('layouts.app')

@section('title', 'Nova Disciplina')

@section('content')
<h2 class="mb-4">Nova Disciplina</h2>
<form action="{{ route('admin.disciplines.store') }}" method="POST">
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
    <div class="mb-3">
        <label for="course_id" class="form-label">Curso *</label>
        <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
            <option value="">Selecione</option>
            @foreach($courses as $c)
            <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>{{ $c->title }} ({{ $c->area->name ?? '' }})</option>
            @endforeach
        </select>
        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="teacher_id" class="form-label">Professor *</label>
        <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
            <option value="">Selecione</option>
            @foreach($teachers as $t)
            <option value="{{ $t->id }}" @selected(old('teacher_id') == $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
        @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('admin.disciplines.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection

