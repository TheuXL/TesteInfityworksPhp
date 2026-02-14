<?php

namespace App\Http\Controllers\Api\v1\Aluno;

use App\Http\Controllers\Controller;
use App\Services\StudentChartDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private StudentChartDataService $chartDataService
    ) {}

    public function index(): JsonResponse
    {
        $chartData = $this->chartDataService->studentChartData(Auth::user());
        return response()->json(['chart_data' => $chartData]);
    }
}
