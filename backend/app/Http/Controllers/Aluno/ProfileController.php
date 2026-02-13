<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
            return redirect()->route('aluno.dashboard')->with('error', 'Cadastro de aluno não encontrado.');
        }
        return view('aluno.profile.edit', compact('student', 'user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
            return redirect()->route('aluno.dashboard')->with('error', 'Cadastro de aluno não encontrado.');
        }
        $student->update($request->only(['name', 'email', 'birth_date']));
        $user->update($request->only(['name', 'email']));
        return redirect()->route('aluno.profile.edit')->with('success', 'Dados atualizados com sucesso.');
    }
}
