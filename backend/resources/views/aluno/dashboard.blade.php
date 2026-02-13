@extends('layouts.app')
@section('title', 'Meu Dashboard')
@section('content')
<h2 class="mb-4">Meu Dashboard</h2>
<div class="row g-4">
<div class="col-12 col-md-6 col-lg-4">
<div class="card h-100">
<div class="card-body">
<h5 class="card-title">Meus cursos</h5>
<canvas id="chartMyCourses" height="200"></canvas>
</div>
</div>
</div>
<div class="col-12 col-md-6 col-lg-4">
<div class="card h-100">
<div class="card-body">
<h5 class="card-title">Minha idade</h5>
<p class="display-6 mb-0">{{ $chartData['my_age']['value'] ?? 0 }} anos</p>
<canvas id="chartMyAge" height="120"></canvas>
</div>
</div>
</div>
<div class="col-12 col-md-6 col-lg-4">
<div class="card h-100">
<div class="card-body">
<h5 class="card-title">Minhas matrículas</h5>
<canvas id="chartMyEnrollments" height="200"></canvas>
</div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    if (chartData.my_courses?.labels?.length) {
        new Chart(document.getElementById('chartMyCourses'), {
            type: 'doughnut',
            data: {
                labels: chartData.my_courses.labels,
                datasets: [{ data: chartData.my_courses.data, backgroundColor: ['#0d6efd','#198754','#ffc107'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
    if (chartData.my_age?.value !== undefined) {
        new Chart(document.getElementById('chartMyAge'), {
            type: 'bar',
            data: {
                labels: ['Idade'],
                datasets: [{ label: 'Anos', data: [chartData.my_age.value], backgroundColor: '#198754' }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }
    if (chartData.my_enrollments?.labels?.length) {
        new Chart(document.getElementById('chartMyEnrollments'), {
            type: 'pie',
            data: {
                labels: chartData.my_enrollments.labels,
                datasets: [{ data: chartData.my_enrollments.data, backgroundColor: ['#0d6efd','#198754','#ffc107'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>
@endpush
