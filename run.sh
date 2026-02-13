#!/bin/sh
# Plataforma Prof. Jubilut - Um comando para subir tudo (Docker)
# Uso: ./run.sh   (ou bash run.sh)

set -e

echo "Plataforma Prof. Jubilut - Iniciando..."

# Verificar Docker
if ! command -v docker >/dev/null 2>&1; then
    echo "ERRO: Docker não encontrado. Instale: https://docs.docker.com/get-docker/"
    exit 1
fi
if ! docker info >/dev/null 2>&1; then
    echo "ERRO: Docker não está rodando. Inicie o Docker."
    exit 1
fi
if ! docker compose version >/dev/null 2>&1; then
    echo "ERRO: Docker Compose não encontrado."
    exit 1
fi

cd "$(dirname "$0")"

echo "Construindo e iniciando containers..."
docker compose up -d --build

echo ""
echo "Aguardando a aplicação (migrações e seed)..."
sleep 10

# Verificar se os containers estão rodando
mysql_running=$(docker inspect -f '{{.State.Running}}' plataforma-mysql 2>/dev/null || echo "false")
app_running=$(docker inspect -f '{{.State.Running}}' plataforma-app 2>/dev/null || echo "false")

if [ "$mysql_running" != "true" ]; then
    echo "ERRO: Container plataforma-mysql não está rodando."
    echo "  Logs: docker compose logs mysql"
    exit 1
fi

if [ "$app_running" != "true" ]; then
    echo "ERRO: Container plataforma-app não está rodando."
    echo "  Últimas linhas do log:"
    docker compose logs --tail=40 app 2>&1
    echo ""
    echo "  Para investigar: docker compose logs -f app"
    exit 1
fi

# Verificar se o app responde
app_ok=""
max_tries=12
tried=0
while [ $tried -lt $max_tries ]; do
    if curl -s -o /dev/null -w "%{http_code}" --connect-timeout 3 http://127.0.0.1:8000 2>/dev/null | grep -q '200\|301\|302'; then
        app_ok="yes"
        break
    fi
    tried=$((tried + 1))
    sleep 2
done

if [ -z "$app_ok" ]; then
    echo "ERRO: A aplicação não respondeu em http://127.0.0.1:8000"
    echo "  Últimas linhas do log do app:"
    docker compose logs --tail=40 app 2>&1
    echo ""
    echo "  Para investigar: docker compose logs -f app"
    exit 1
fi

echo "Verificação: MySQL e app estão rodando e a aplicação responde."
echo ""
echo "========================================"
echo "  Aplicação disponível em:"
echo "  http://127.0.0.1:8000"
echo "========================================"
echo ""
echo "Credenciais:"
echo "  Admin : admin@plataforma.test / password"
echo "  Aluno : emanuel@plataforma.test / password"
echo ""
echo "Para parar: docker compose down"
echo "Para ver logs: docker compose logs -f app"
