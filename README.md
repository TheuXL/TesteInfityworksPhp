# Plataforma Prof. Jubilut

Aplicação Laravel para gestão escolar (cursos, alunos, professores, disciplinas, matrículas) com área administrativa e área do aluno.

## Clone e rode (recomendado – Docker)

**Requisito:** [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado e em execução.

```powershell
# Windows (PowerShell)
.\run.ps1
```

```bash
# Linux / macOS
chmod +x run.sh
./run.sh
```

O script verifica o Docker, sobe os containers (app + MySQL), roda as migrações e o seed. Ao terminar, acesse:

- **URL:** http://127.0.0.1:8000  
- **Admin:** `admin@plataforma.test` / `password`  
- **Aluno:** `emanuel@plataforma.test` / `password`  

Para parar: `docker compose down`  
Para ver logs: `docker compose logs -f app`

---

## Rodar sem Docker (desenvolvimento local)

1. Entre na pasta do backend: `cd backend`
2. Instale as dependências: `composer install`
3. Copie o ambiente: `copy .env.example .env` (Windows) ou `cp .env.example .env` (Linux/Mac)
4. Gere a chave: `php artisan key:generate`
5. Configure o banco no `.env` (MySQL ou SQLite) e rode: `php artisan migrate --seed`
6. Suba o servidor: `php artisan serve`

Detalhes e solução de erros comuns em **backend/INSTALACAO.md**.

---

## Estrutura

- **backend/** – Aplicação Laravel (API, Blade, migrations, testes)
- **docker-compose.yml** – Serviços app + MySQL para Docker
- **run.ps1** / **run.sh** – Script único para subir o projeto com Docker
