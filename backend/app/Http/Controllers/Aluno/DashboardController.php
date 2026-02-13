<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Services\StudentChartDataService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private StudentChartDataService $chartDataService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $chartData = $this->chartDataService->studentChartData($user);
        return view('aluno.dashboard', compact('chartData'));
    }
}
