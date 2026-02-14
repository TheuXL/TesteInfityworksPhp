<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Services\StudentChartDataService;

class DashboardController extends Controller
{
    public function __construct(
        private StudentChartDataService $chartDataService
    ) {}

    public function index()
    {
        return response()->json([
            'chart_data' => $this->chartDataService->adminChartData(),
        ]);
    }
}
