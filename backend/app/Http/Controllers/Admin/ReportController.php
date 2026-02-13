<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\StudentChartDataService;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private StudentChartDataService $chartDataService
    ) {}

    public function index()
    {
        $report = $this->reportService->courseAgesReport();
        $chartData = $this->chartDataService->adminChartData();
        return view('admin.reports.index', compact('report', 'chartData'));
    }
}
