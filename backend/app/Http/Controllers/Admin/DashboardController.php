<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StudentChartDataService;

class DashboardController extends Controller
{
    public function __construct(
        private StudentChartDataService $chartDataService
    ) {}

    public function index()
    {
        $chartData = $this->chartDataService->adminChartData();
        return view('admin.dashboard', compact('chartData'));
    }
}
