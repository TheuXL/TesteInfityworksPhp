<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma Prof. Jubilut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100 bg-light">
    <div class="container text-center">
        <h1 class="mb-4">Plataforma Prof. Jubilut</h1>
        <p class="lead text-muted mb-4">Ensino online</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Entrar</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Criar conta (Aluno)</a>
        </div>
    </div>
</body>
</html>
