@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Alunos por curso</h5>
                <canvas id="chartStudentsPerCourse" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Idade média por curso</h5>
                <canvas id="chartAverageAgePerCourse" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Alunos por faixa etária</h5>
                <canvas id="chartStudentsByAgeRange" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Matrículas por curso</h5>
                <canvas id="chartEnrollmentsPerCourse" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Alunos por área</h5>
                <canvas id="chartStudentsPerArea" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    const colors = ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1'];

    if (chartData.students_per_course?.labels?.length) {
        new Chart(document.getElementById('chartStudentsPerCourse'), {
            type: 'bar',
            data: {
                labels: chartData.students_per_course.labels,
                datasets: [{ label: 'Alunos', data: chartData.students_per_course.data, backgroundColor: colors[0] }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    if (chartData.average_age_per_course?.labels?.length) {
        new Chart(document.getElementById('chartAverageAgePerCourse'), {
            type: 'bar',
            data: {
                labels: chartData.average_age_per_course.labels,
                datasets: [{ label: 'Idade média', data: chartData.average_age_per_course.data, backgroundColor: colors[1] }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    if (chartData.students_by_age_range?.labels?.length) {
        new Chart(document.getElementById('chartStudentsByAgeRange'), {
            type: 'doughnut',
            data: {
                labels: chartData.students_by_age_range.labels,
                datasets: [{ data: chartData.students_by_age_range.data, backgroundColor: colors }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
    if (chartData.enrollments_per_course?.labels?.length) {
        new Chart(document.getElementById('chartEnrollmentsPerCourse'), {
            type: 'pie',
            data: {
                labels: chartData.enrollments_per_course.labels,
                datasets: [{ data: chartData.enrollments_per_course.data, backgroundColor: colors }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
    if (chartData.students_per_area?.labels?.length) {
        new Chart(document.getElementById('chartStudentsPerArea'), {
            type: 'bar',
            data: {
                labels: chartData.students_per_area.labels,
                datasets: [{ label: 'Alunos', data: chartData.students_per_area.data, backgroundColor: colors[4] }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } } }
        });
    }
});
</script>
@endpush
