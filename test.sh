#!/bin/sh
# Rodar testes PHPUnit dentro do Docker (monta código do host, instala deps se preciso)
# Uso: ./test.sh   (a partir da raiz do projeto)  -- Linux/macOS/Git Bash
# No Windows PowerShell use: .\test.ps1
# Não precisa ter os containers da aplicação rodando.

set -e

if ! command -v docker >/dev/null 2>&1; then
    echo "ERRO: Docker não encontrado."
    exit 1
fi

cd "$(dirname "$0")"
root="$(pwd)"
backend="${root}/backend"

if [ ! -d "$backend" ]; then
    echo "ERRO: Pasta backend não encontrada em $root"
    exit 1
fi

# Garantir que a imagem existe (build se não existir)
image_name="testeinfityworksphp-app:latest"
if [ -z "$(docker images -q "$image_name" 2>/dev/null)" ]; then
    echo "Imagem não encontrada. Construindo..."
    docker compose build app || { echo "ERRO: Falha ao construir. Rode ./run.sh uma vez antes."; exit 1; }
fi

echo "Rodando testes no Docker (código em backend/)..."
backend_abs="$(cd "$backend" && pwd)"
docker run --rm \
    -v "${backend_abs}:/var/www" \
    -w /var/www \
    -e APP_ENV=testing \
    -e DB_CONNECTION=sqlite \
    -e DB_DATABASE=:memory: \
    --entrypoint sh \
    "$image_name" \
    -c "composer install --no-interaction --ignore-platform-reqs -q 2>/dev/null || true; php vendor/bin/phpunit --testdox"
