# Rodar testes PHPUnit dentro do Docker (monta codigo do host, instala deps se preciso)
# Uso: .\test.ps1   (a partir da raiz do projeto)
# Nao precisa ter os containers da aplicacao rodando.

$ErrorActionPreference = "Stop"

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "ERRO: Docker nao encontrado." -ForegroundColor Red
    exit 1
}

$root = $PSScriptRoot
$backendPath = Join-Path $root "backend"

if (-not (Test-Path $backendPath)) {
    Write-Host "ERRO: Pasta backend nao encontrada em $root" -ForegroundColor Red
    exit 1
}

# Garantir que a imagem existe (build se nao existir)
$imageName = "testeinfityworksphp-app:latest"
$imageExists = docker images -q $imageName 2>$null
if (-not $imageExists) {
    Write-Host "Imagem nao encontrada. Construindo..." -ForegroundColor Yellow
    Set-Location $root
    docker compose build app 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "ERRO: Falha ao construir a imagem. Rode .\run.ps1 uma vez antes." -ForegroundColor Red
        exit 1
    }
}

Write-Host "Rodando testes no Docker (codigo em backend/)..." -ForegroundColor Cyan
$backendAbs = (Resolve-Path $backendPath).Path
docker run --rm `
    -v "${backendAbs}:/var/www" `
    -w /var/www `
    -e APP_ENV=testing `
    -e APP_KEY=base64:PGOngFuRa/3nYai4hUxfCUI5uyC7JPyhAZQy6n7QJjI= `
    -e FRONTEND_URL=http://localhost:5173 `
    -e DB_CONNECTION=sqlite `
    -e DB_DATABASE=:memory: `
    --entrypoint sh `
    $imageName `
    -c "composer install --no-interaction --ignore-platform-reqs -q 2>/dev/null || true; php vendor/bin/phpunit --testdox"

exit $LASTEXITCODE
