<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação da API – Plataforma Prof. Jubilut</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; color: #1e293b; line-height: 1.6; }
        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.15rem; margin-top: 1.5rem; }
        code { background: #f1f5f9; padding: 0.15em 0.35em; border-radius: 4px; font-size: 0.88em; }
        pre { background: #f8fafc; padding: 0.75rem; border-radius: 6px; overflow-x: auto; font-size: 0.85rem; }
        table { width: 100%; border-collapse: collapse; margin: 0.5rem 0; }
        th, td { text-align: left; padding: 0.4rem 0.6rem; border-bottom: 1px solid #e2e8f0; }
        th { font-weight: 600; }
        .method { font-weight: 600; }
        .method.get { color: #059669; }
        .method.post { color: #2563eb; }
        .method.put { color: #d97706; }
        .method.delete { color: #dc2626; }
        a { color: #2563eb; }
    </style>
</head>
<body>
    <h1>Documentação da API v1</h1>
    <p>Base URL: <code>{{ url('/api/v1') }}</code>. Autenticação: sessão (cookie) via Sanctum. Obtenha o cookie CSRF em <code>GET /sanctum/csrf-cookie</code> antes de <code>POST /login</code>.</p>

    <h2>Autenticação (web)</h2>
    <table>
        <tr><th>Método</th><th>URL</th><th>Descrição</th></tr>
        <tr><td class="method post">POST</td><td><code>/login</code></td><td>Login (email, password). Retorna JSON com <code>user</code> ou 422.</td></tr>
        <tr><td class="method post">POST</td><td><code>/register</code></td><td>Registro (name, email, password, password_confirmation). Retorna 201 + user.</td></tr>
        <tr><td class="method post">POST</td><td><code>/logout</code></td><td>Logout (requer auth).</td></tr>
    </table>

    <h2>Usuário autenticado</h2>
    <table>
        <tr><td class="method get">GET</td><td><code>/api/v1/user</code></td><td>Retorna o usuário logado (com <code>student</code> se for aluno). Requer auth.</td></tr>
    </table>

    <h2>Admin (role: admin)</h2>
    <table>
        <tr><th>Método</th><th>URL</th><th>Descrição</th></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/admin/dashboard</code></td><td>Dados dos gráficos do dashboard.</td></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/admin/reports</code></td><td>Relatório idade por curso + chart_data.</td></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/admin/areas</code></td><td>Lista áreas (paginado).</td></tr>
        <tr><td class="method post">POST</td><td><code>/api/v1/admin/areas</code></td><td>Criar área (name).</td></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/admin/areas/{id}</code></td><td>Uma área.</td></tr>
        <tr><td class="method put">PUT</td><td><code>/api/v1/admin/areas/{id}</code></td><td>Atualizar área (name).</td></tr>
        <tr><td class="method delete">DELETE</td><td><code>/api/v1/admin/areas/{id}</code></td><td>Excluir área.</td></tr>
        <tr><td colspan="3">Recursos análogos: <code>courses</code>, <code>teachers</code>, <code>disciplines</code>, <code>students</code>, <code>enrollments</code>. GET index, POST store, GET show, PUT update, DELETE destroy. <code>enrollments</code> tem ainda <code>GET /api/v1/admin/enrollments/create</code> (lista alunos e cursos).</td></tr>
    </table>

    <h2>Aluno (role: student)</h2>
    <table>
        <tr><th>Método</th><th>URL</th><th>Descrição</th></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/aluno/dashboard</code></td><td>Dados dos gráficos do dashboard do aluno.</td></tr>
        <tr><td class="method get">GET</td><td><code>/api/v1/aluno/profile</code></td><td>Dados do perfil (user + student).</td></tr>
        <tr><td class="method put">PUT</td><td><code>/api/v1/aluno/profile</code></td><td>Atualizar perfil (name, email, birth_date).</td></tr>
    </table>

    <p><a href="{{ url('/') }}">Voltar</a></p>
</body>
</html>
