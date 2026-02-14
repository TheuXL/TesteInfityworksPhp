<?php

namespace App\Http\Controllers\Api\v1\Aluno;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(): JsonResponse
    {
        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
            return response()->json(['message' => 'Cadastro de aluno não encontrado.'], 404);
        }
        return response()->json([
            'user' => new UserResource($user),
            'student' => new StudentResource($student),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \App\Models\Student|null $student */
        $student = $user->student;
        if (! $student) {
            return response()->json(['message' => 'Cadastro de aluno não encontrado.'], 404);
        }
        $student->update($request->only(['name', 'email', 'birth_date']));
        $user->update($request->only(['name', 'email']));
        return response()->json([
            'user' => new UserResource($user->fresh()),
            'student' => new StudentResource($student->fresh()),
        ]);
    }
}
