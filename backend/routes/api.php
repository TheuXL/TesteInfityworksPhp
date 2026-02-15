<?php

use App\Http\Controllers\Api\v1\Admin\AreaController as ApiAreaController;
use App\Http\Controllers\Api\v1\Admin\CourseController as ApiCourseController;
use App\Http\Controllers\Api\v1\Admin\DashboardController as ApiAdminDashboardController;
use App\Http\Controllers\Api\v1\Admin\DisciplineController as ApiDisciplineController;
use App\Http\Controllers\Api\v1\Admin\EnrollmentController as ApiEnrollmentController;
use App\Http\Controllers\Api\v1\Admin\RegisterAdminController;
use App\Http\Controllers\Api\v1\Admin\ReportController as ApiReportController;
use App\Http\Controllers\Api\v1\Admin\StudentController as ApiStudentController;
use App\Http\Controllers\Api\v1\Admin\TeacherController as ApiTeacherController;
use App\Http\Controllers\Api\v1\Aluno\DashboardController as ApiAlunoDashboardController;
use App\Http\Controllers\Api\v1\Aluno\ProfileController as ApiProfileController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return new UserResource($request->user()->load('student'));
    });

    Route::middleware(['auth:sanctum', 'role.admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [ApiAdminDashboardController::class, 'index']);
        Route::get('/reports', [ApiReportController::class, 'index']);
        Route::get('/reports/pdf', [ApiReportController::class, 'pdf']);
        Route::apiResource('areas', ApiAreaController::class);
        Route::apiResource('courses', ApiCourseController::class);
        Route::apiResource('teachers', ApiTeacherController::class);
        Route::apiResource('disciplines', ApiDisciplineController::class);
        Route::apiResource('students', ApiStudentController::class);
        Route::get('enrollments/create', [ApiEnrollmentController::class, 'create']);
        Route::apiResource('enrollments', ApiEnrollmentController::class);
        Route::post('register', [RegisterAdminController::class, 'store']);
    });

    Route::middleware(['auth:sanctum', 'role.student'])->prefix('aluno')->group(function () {
        Route::get('/dashboard', [ApiAlunoDashboardController::class, 'index']);
        Route::get('/profile', [ApiProfileController::class, 'edit']);
        Route::put('/profile', [ApiProfileController::class, 'update']);
    });
});
