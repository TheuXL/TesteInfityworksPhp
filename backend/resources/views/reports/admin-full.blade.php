<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Admin - Alunos e Gráficos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 16px; }
        h1 { font-size: 18px; margin-bottom: 4px; color: #1e293b; }
        .date { font-size: 10px; color: #64748b; margin-bottom: 16px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 8px; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: 600; }
        tr:nth-child(even) { background: #f8fafc; }
        .summary-grid { display: table; width: 100%; margin-bottom: 16px; }
        .summary-item { display: table-cell; padding: 8px 12px; background: #f1f5f9; border: 1px solid #e2e8f0; margin-right: 8px; }
        .summary-label { font-size: 9px; text-transform: uppercase; color: #64748b; }
        .summary-value { font-size: 14px; font-weight: 600; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <h1>Relatório completo – Dashboard Admin</h1>
    <p class="date">Gerado em {{ $date }}</p>

    <h2>Resumo geral</h2>
    <table class="summary-table">
        <tr>
            <th>Alunos</th>
            <th>Matrículas</th>
            <th>Cursos</th>
            <th>Professores</th>
            <th>Áreas</th>
            <th>Disciplinas</th>
        </tr>
        <tr>
            <td>{{ $chartData['summary']['students'] ?? 0 }}</td>
            <td>{{ $chartData['summary']['enrollments'] ?? 0 }}</td>
            <td>{{ $chartData['summary']['courses'] ?? 0 }}</td>
            <td>{{ $chartData['summary']['teachers'] ?? 0 }}</td>
            <td>{{ $chartData['summary']['areas'] ?? 0 }}</td>
            <td>{{ $chartData['summary']['disciplines'] ?? 0 }}</td>
        </tr>
    </table>

    <h2>Idade por curso (média, mais novo, mais velho)</h2>
    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Média de idade</th>
                <th>Aluno mais novo</th>
                <th>Aluno mais velho</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $row)
            <tr>
                <td>{{ $row['course']['title'] ?? '–' }}</td>
                <td>{{ $row['average_age'] ?? '–' }}</td>
                <td>{{ isset($row['youngest']) ? $row['youngest']['name'] . ' (' . $row['youngest']['age'] . ' anos)' : '–' }}</td>
                <td>{{ isset($row['oldest']) ? $row['oldest']['name'] . ' (' . $row['oldest']['age'] . ' anos)' : '–' }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Nenhum dado.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Alunos por curso</h2>
    <table>
        <thead>
            <tr><th>Curso</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['students_per_course']['labels'] ?? []; $data = $chartData['students_per_course']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Média de idade por curso</h2>
    <table>
        <thead>
            <tr><th>Curso</th><th>Média de idade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['average_age_per_course']['labels'] ?? []; $data = $chartData['average_age_per_course']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? '–' }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Alunos por faixa etária</h2>
    <table>
        <thead>
            <tr><th>Faixa</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['students_by_age_range']['labels'] ?? []; $data = $chartData['students_by_age_range']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Matrículas por curso</h2>
    <table>
        <thead>
            <tr><th>Curso</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['enrollments_per_course']['labels'] ?? []; $data = $chartData['enrollments_per_course']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Alunos por área</h2>
    <table>
        <thead>
            <tr><th>Área</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['students_per_area']['labels'] ?? []; $data = $chartData['students_per_area']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Matrículas por mês</h2>
    <table>
        <thead>
            <tr><th>Mês</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['enrollments_per_month']['labels'] ?? []; $data = $chartData['enrollments_per_month']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Alunos por mês</h2>
    <table>
        <thead>
            <tr><th>Mês</th><th>Quantidade</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['students_per_month']['labels'] ?? []; $data = $chartData['students_per_month']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Disciplinas por curso (até 10)</h2>
    <table>
        <thead>
            <tr><th>Curso</th><th>Disciplinas</th></tr>
        </thead>
        <tbody>
            @php $labels = $chartData['disciplines_per_course']['labels'] ?? []; $data = $chartData['disciplines_per_course']['data'] ?? []; @endphp
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data[$i] ?? 0 }}</td>
            </tr>
            @endforeach
            @if(empty($labels))
            <tr><td colspan="2">Nenhum dado.</td></tr>
            @endif
        </tbody>
    </table>
</body>
</html>
