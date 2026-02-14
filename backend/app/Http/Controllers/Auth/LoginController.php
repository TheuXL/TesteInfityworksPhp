<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return redirect()->away(config('app.frontend_url', 'http://localhost:5173') . '/login');
    }

    public function login(Request $request)
    {
        return $this->attemptAndRespond($request, null);
    }

    /** Login para área do aluno: só aceita usuários com role student. */
    public function loginAluno(Request $request)
    {
        return $this->attemptAndRespond($request, UserRole::STUDENT);
    }

    /** Login para área admin: só aceita usuários com role admin. */
    public function loginAdmin(Request $request)
    {
        return $this->attemptAndRespond($request, UserRole::ADMIN);
    }

    private function attemptAndRespond(Request $request, ?UserRole $requireRole)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Credenciais inválidas.'], 422);
            }
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('student');

        if ($requireRole !== null && $user->role !== $requireRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $message = $requireRole === UserRole::ADMIN
                ? 'Use a área de administrador para entrar.'
                : 'Use a área do aluno para entrar.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        if ($request->wantsJson()) {
            return response()->json(['user' => new \App\Http\Resources\UserResource($user)]);
        }
        $base = config('app.frontend_url', 'http://localhost:5173');
        if ($user->role === UserRole::ADMIN) {
            return redirect()->intended($base . '/admin/dashboard');
        }
        return redirect()->intended($base . '/aluno/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logout realizado.']);
        }
        return redirect()->away(config('app.frontend_url', 'http://localhost:5173') . '/login');
    }
}
