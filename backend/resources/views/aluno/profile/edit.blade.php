@extends('layouts.app')
@section('title', 'Meu perfil')
@section('content')
<h2 class="mb-4">Atualizar meus dados</h2>
<form action="{{ route('aluno.profile.update') }}" method="POST">
@csrf
@method('PUT')
<div class="mb-3">
<label for="name" class="form-label">Nome *</label>
<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $student->name) }}" required>
@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
<label for="email" class="form-label">E-mail *</label>
<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $student->email) }}" required>
@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
<label for="birth_date" class="form-label">Data de nascimento</label>
<input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
@error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-primary">Salvar</button>
<a href="{{ route('aluno.dashboard') }}" class="btn btn-secondary">Voltar</a>
</form>
@endsection
