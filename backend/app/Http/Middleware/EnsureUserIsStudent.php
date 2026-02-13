<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== \App\Enums\UserRole::STUDENT) {
            abort(403, 'Acesso restrito ao aluno.');
        }

        return $next($request);
    }
}
