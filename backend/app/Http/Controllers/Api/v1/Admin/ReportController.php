<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\ReportService;
use App\Services\StudentChartDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response as ResponseFacade;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private StudentChartDataService $chartDataService
    ) {}

    public function index(Request $request)
    {
        $report = $this->reportService->courseAgesReport();
        $chartData = $this->chartDataService->adminChartData();
        $rows = $report->map(function ($row) use ($request) {
            return [
                'course' => (new CourseResource($row['course']))->toArray($request),
                'average_age' => $row['average_age'],
                'youngest' => $row['youngest'] ?? null,
                'oldest' => $row['oldest'] ?? null,
            ];
        });
        return ResponseFacade::json([
            'report' => $rows,
            'chart_data' => $chartData,
        ]);
    }

    /**
     * Gera um PDF com todos os dados e tabelas equivalentes aos gráficos do dashboard admin.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function pdf(Request $request)
    {
        $report = $this->reportService->courseAgesReport();
        $chartData = $this->chartDataService->adminChartData();
        $rows = $report->map(function ($row) use ($request) {
            return [
                'course' => (new CourseResource($row['course']))->toArray($request),
                'average_age' => $row['average_age'],
                'youngest' => $row['youngest'] ?? null,
                'oldest' => $row['oldest'] ?? null,
            ];
        });

        $date = Carbon::now()->locale('pt_BR')->isoFormat('DD/MM/YYYY [às] HH:mm');

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.admin-full', [
            'report' => $rows,
            'chartData' => $chartData,
            'date' => $date,
        ])->setPaper('a4');

        $filename = 'relatorio-admin-alunos-' . Carbon::now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
