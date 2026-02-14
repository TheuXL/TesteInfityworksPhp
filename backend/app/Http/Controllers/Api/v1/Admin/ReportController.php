<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
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
        $rows = $report->map(function ($row) {
            return [
                'course' => (new CourseResource($row['course']))->resolve(),
                'average_age' => $row['average_age'],
                'youngest' => $row['youngest'] ?? null,
                'oldest' => $row['oldest'] ?? null,
            ];
        });
        return response()->json([
            'report' => $rows,
            'chart_data' => $chartData,
        ]);
    }
}
