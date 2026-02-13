@extends('layouts.app')
@section('title', 'Relatórios')
@section('content')
<h2 class="mb-4">Relatório: Idade por curso</h2>
<div class="table-responsive mb-4">
<table class="table table-striped">
<thead>
<tr><th>Curso</th><th>Média de idade</th><th>Aluno mais novo</th><th>Aluno mais velho</th></tr>
</thead>
<tbody>
@foreach($report as $row)
<tr>
<td>{{ $row['course']->title }}</td>
<td>{{ $row['average_age'] }}</td>
<td>@if($row['youngest']) {{ $row['youngest']['name'] }} ({{ $row['youngest']['age'] }} anos) @else - @endif</td>
<td>@if($row['oldest']) {{ $row['oldest']['name'] }} ({{ $row['oldest']['age'] }} anos) @else - @endif</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="row">
<div class="col-12 col-lg-6 mb-4">
<div class="card">
<div class="card-body">
<h5 class="card-title">Idade média por curso</h5>
<canvas id="reportChartAges" height="250"></canvas>
</div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var report = @json($report);
    var labels = report.map(function(r) { return r.course.title; });
    var data = report.map(function(r) { return r.average_age; });
    if (labels.length) {
        new Chart(document.getElementById('reportChartAges'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Idade média', data: data, backgroundColor: '#0d6efd' }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>
@endpush
