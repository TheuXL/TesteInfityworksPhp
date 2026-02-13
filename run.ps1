# Plataforma Prof. Jubilut - Um comando para subir tudo (Docker)
# Uso: .\run.ps1   ou   pwsh -File run.ps1

$ErrorActionPreference = "Stop"

Write-Host "Plataforma Prof. Jubilut - Iniciando..." -ForegroundColor Cyan

# Verificar se Docker está instalado
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "ERRO: Docker nao esta instalado." -ForegroundColor Red
    Write-Host "  Instale o Docker Desktop: https://www.docker.com/products/docker-desktop/" -ForegroundColor Yellow
    exit 1
}

# Verificar se o Docker está rodando (Docker Desktop iniciado)
# Usa cmd para evitar que WARNINGs do Docker no stderr disparem erro no PowerShell
cmd /c "docker info 2>nul"
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERRO: Docker nao esta rodando." -ForegroundColor Red
    Write-Host "  Inicie o Docker Desktop e aguarde ele ficar pronto, depois rode .\run.ps1 novamente." -ForegroundColor Yellow
    exit 1
}

# Verificar docker compose
if (-not (docker compose version 2>$null)) {
    Write-Host "ERRO: Docker Compose nao encontrado. Use Docker Desktop atualizado." -ForegroundColor Red
    exit 1
}

$root = $PSScriptRoot
Set-Location $root

Write-Host "Construindo e iniciando containers..." -ForegroundColor Green
docker compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Host "Falha ao subir os containers." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Aguardando a aplicacao ficar pronta (migracoes e seed)..." -ForegroundColor Yellow
Start-Sleep -Seconds 8

# Verificar se os containers estao rodando
$mysqlRunning = docker inspect -f '{{.State.Running}}' plataforma-mysql 2>$null
$appRunning = docker inspect -f '{{.State.Running}}' plataforma-app 2>$null

if ($mysqlRunning -ne "true") {
    Write-Host "ERRO: Container plataforma-mysql nao esta rodando." -ForegroundColor Red
    Write-Host "  Logs: docker compose logs mysql" -ForegroundColor Yellow
    exit 1
}

if ($appRunning -ne "true") {
    Write-Host "ERRO: Container plataforma-app nao esta rodando." -ForegroundColor Red
    Write-Host "  Ultimas linhas do log:" -ForegroundColor Yellow
    docker compose logs --tail=40 app 2>&1
    Write-Host ""
    Write-Host "  Para investigar: docker compose logs -f app" -ForegroundColor Yellow
    exit 1
}

# Verificar se o app responde
$maxTries = 12
$tried = 0
$appResponded = $false
do {
    try {
        $null = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -UseBasicParsing -TimeoutSec 3 -ErrorAction Stop
        $appResponded = $true
        break
    } catch {
        $tried++
        if ($tried -ge $maxTries) {
            break
        }
        Start-Sleep -Seconds 2
    }
} while ($tried -lt $maxTries)

if (-not $appResponded) {
    Write-Host "ERRO: A aplicacao nao respondeu em http://127.0.0.1:8000" -ForegroundColor Red
    Write-Host "  Ultimas linhas do log do app:" -ForegroundColor Yellow
    docker compose logs --tail=40 app 2>&1
    Write-Host ""
    Write-Host "  Para investigar: docker compose logs -f app" -ForegroundColor Yellow
    exit 1
}

Write-Host "Verificacao: MySQL e app estao rodando e a aplicacao responde." -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Aplicacao disponivel em:" -ForegroundColor White
Write-Host "  http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Credenciais:" -ForegroundColor White
Write-Host "  Admin : admin@plataforma.test / password" -ForegroundColor Gray
Write-Host "  Aluno : emanuel@plataforma.test / password" -ForegroundColor Gray
Write-Host ""
Write-Host "Para parar: docker compose down" -ForegroundColor DarkGray
Write-Host "Para ver logs: docker compose logs -f app" -ForegroundColor DarkGray
