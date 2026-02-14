<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return redirect()->away(config('app.frontend_url', 'http://localhost:5173') . '/register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:students,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birth_date' => ['required', 'date', 'before:today'],
        ], [
            'email.unique' => 'Este e-mail já está em uso.',
            'name.required' => 'O nome é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'Informe uma data válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => UserRole::STUDENT,
                ]);
                \App\Models\Student::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'birth_date' => $validated['birth_date'],
                    'user_id' => $user->id,
                ]);
                return $user;
            });
            event(new Registered($user));
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Erro ao cadastrar. Tente outro e-mail.',
                    'errors' => ['email' => ['Este e-mail pode já estar em uso ou ocorreu um erro.']],
                ], 422);
            }
            throw $e;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Cadastro realizado com sucesso. Faça login com suas credenciais.',
            ], 201);
        }

        Auth::login($user);
        return redirect()->away(config('app.frontend_url', 'http://localhost:5173') . '/aluno/dashboard');
    }
}
