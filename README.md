# **Plataforma Prof. Jubilut – Projeto Completo**

Bem-vindo ao repositório da **Plataforma Prof. Jubilut**: uma aplicação completa de gestão escolar com **área administrativa** e **área do aluno**. O projeto é dividido em **backend** (API Laravel + MySQL) e **frontend** (SPA Vue 3), que se comunicam via API REST com autenticação por sessão (Laravel Sanctum). Este README descreve o projeto como um todo, como rodar com **Docker** ou em **ambiente local**, configuração de **.env**, banco de dados e onde encontrar a documentação detalhada de cada parte.

## 📋 Índice

- [Visão Geral do Projeto](#-visão-geral-do-projeto)
- [Arquitetura Geral](#-arquitetura-geral)
- [Pré-requisitos](#-pré-requisitos)
- [Quick Start (Docker + Frontend)](#-quick-start-docker--frontend)
- [Backend: Configuração e Execução](#-backend-configuração-e-execução)
- [Frontend: Configuração e Execução](#-frontend-configuração-e-execução)
- [Banco de Dados (MySQL)](#-banco-de-dados-mysql)
- [Docker: Serviços e Scripts](#-docker-serviços-e-scripts)
- [Variáveis de Ambiente (.env)](#-variáveis-de-ambiente-env)
- [Credenciais Padrão](#-credenciais-padrão)
- [Estrutura do Repositório](#-estrutura-do-repositório)
- [Documentação Detalhada](#-documentação-detalhada)
- [Solução de Problemas](#-solução-de-problemas)

---

## 🚀 Visão Geral do Projeto

A Plataforma Prof. Jubilut é composta por:

1. **Backend (Laravel 10)** – API REST em PHP que gerencia usuários (admin e aluno), áreas, cursos, professores, disciplinas, alunos e matrículas. Oferece relatórios (idade média por curso, aluno mais novo/mais velho), dados para gráficos nos dashboards, autenticação via **Laravel Sanctum** (sessão/cookies) e proteção de rotas por papel (`admin` e `student`). Persistência em **MySQL** com todas as tabelas criadas por **migrations**.

2. **Frontend (Vue 3 SPA)** – Interface única em Vue 3 + Vue Router + Pinia + Tailwind + ApexCharts. Duas áreas: **Admin** (dashboard com gráficos, CRUD de todas as entidades, relatórios, cadastro de administrador) e **Aluno** (dashboard com “meus cursos”, “minha idade”, “minhas matrículas”, e edição do próprio perfil). Consome a API do backend; em desenvolvimento o **Vite** faz proxy das requisições para o Laravel.

3. **Docker** – Opcional: sobe o **backend** (PHP 8.2 + Laravel) e o **MySQL 8** em containers. O frontend continua rodando na sua máquina (`npm run dev`) e usa o proxy para falar com o backend na porta 8000.

### O que o sistema oferece

- **Login único** com redirecionamento por papel (admin → `/admin/dashboard`, aluno → `/aluno/dashboard`).
- **Cadastro de aluno** (público em `/register`) e **cadastro de admin** (apenas por admin logado em `/admin/register`).
- **CRUD completo** para Áreas, Cursos, Professores, Disciplinas, Alunos e Matrículas (área admin).
- **Relatórios** com tabela (média de idade por curso, mais novo, mais velho) e dados para gráficos.
- **Dashboards** com vários gráficos (admin: alunos por curso, por faixa etária, por área, matrículas por mês, etc.; aluno: meus cursos, minha idade, minhas matrículas).
- **Filtro** de alunos por nome e e-mail na listagem admin.
- **Responsivo** e preparado para uso em mobile.

---

## 🏗️ Arquitetura Geral

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         USUÁRIO (navegador)                              │
└─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  FRONTEND (Vue 3 SPA)                                                    │
│  http://localhost:5173                                                   │
│  • Vue Router (/, /login, /register, /admin/*, /aluno/*)                │
│  • Pinia (store auth: user, isAdmin, isStudent)                          │
│  • Axios → proxy /api, /login, /register, /logout, /sanctum → Backend   │
│  • Páginas: Login, Register, Dashboard Admin, CRUDs, Reports,            │
│             Dashboard Aluno, Perfil Aluno                                │
└─────────────────────────────────────────────────────────────────────────┘
                                      │
                    HTTP (cookies de sessão Sanctum)
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  BACKEND (Laravel 10 API)                                                │
│  http://127.0.0.1:8000  (ou container Docker)                           │
│  • Rotas web: GET /, /docs, POST /login, /register, /logout             │
│  • API v1: /api/v1/user, /api/v1/admin/*, /api/v1/aluno/*               │
│  • Middleware: auth:sanctum, role.admin, role.student                    │
│  • Services: ReportService, StudentChartDataService                      │
│  • Models: User, Student, Area, Course, Teacher, Discipline, Enrollment   │
└─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  MYSQL 8                                                                 │
│  localhost:3306  (ou container Docker)                                   │
│  • Banco: plataforma (Docker) ou laravel/plataforma (local)              │
│  • Tabelas: users, students, areas, courses, teachers, disciplines,       │
│             enrollments, + migrations Laravel (sessions, cache, etc.)   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📌 Pré-requisitos

### Para rodar com Docker (recomendado para backend + banco)

- **Docker Desktop** instalado e em execução ([download](https://www.docker.com/products/docker-desktop/)).
- **Node.js** (v18 ou superior) e **npm** – para rodar o frontend localmente.

### Para rodar tudo local (sem Docker)

- **PHP** 8.1 ou 8.2 (extensões: pdo_mysql, mbstring, openssl, xml, etc.).
- **Composer**.
- **MySQL** 8.x (ou MariaDB compatível).
- **Node.js** (v18+) e **npm**.

---

## ⚡ Quick Start (Docker + Frontend)

A forma mais rápida é: **backend e MySQL no Docker**, **frontend na sua máquina**.

### 1. Subir backend e MySQL com Docker

**Windows (PowerShell):**
```powershell
.\run.ps1
```

**Linux / macOS:**
```bash
chmod +x run.sh
./run.sh
```

O script verifica o Docker, faz `docker compose up -d --build`, aguarda o MySQL ficar saudável e o app rodar migrações e seed. Ao final, o backend estará em **http://127.0.0.1:8000**.

### 2. Subir o frontend

```bash
cd frontend
npm install
npm run dev
```

Acesse **http://localhost:5173**. O Vite já está configurado para fazer proxy de `/api`, `/login`, `/register`, `/logout` e `/sanctum` para `http://127.0.0.1:8000`, então a SPA conseguirá fazer login e chamar a API.

### 3. Acessar a aplicação

- **URL da interface:** http://localhost:5173  
- **Admin:** `admin@plataforma.test` / `password`  
- **Aluno:** `emanuel@plataforma.test` / `password`  

Para parar os containers: `docker compose down`.  
Para ver logs do backend: `docker compose logs -f app`.

---

## 🔧 Backend: Configuração e Execução

O backend fica na pasta **`backend/`**. Documentação completa em **[backend/README.md](backend/README.md)**.

### Execução com Docker

O container **plataforma-app** é construído a partir de `backend/Dockerfile`. Ele:

- Usa PHP 8.2-cli e instala extensões (pdo_mysql, mbstring, zip, etc.) e Composer.
- Copia o código, usa `backend/.env.docker` como base do `.env` (no entrypoint), roda `composer install`, e no **docker-entrypoint.sh** espera o MySQL, gera `APP_KEY` se necessário, executa `php artisan migrate --force` e `php artisan db:seed --force`, e inicia `php artisan serve --host=0.0.0.0 --port=8000`.

As variáveis de ambiente do backend no Docker vêm do `docker-compose.yml` e do `.env.docker` (copiado para `.env` na imagem). Não é obrigatório ter um `.env` na pasta `backend/` ao usar apenas Docker.

### Execução local (sem Docker)

1. Entre na pasta do backend:
   ```bash
   cd backend
   ```
2. Instale as dependências:
   ```bash
   composer install
   ```
3. Crie o arquivo de ambiente:
   ```bash
   copy .env.example .env   # Windows
   cp .env.example .env     # Linux/macOS
   ```
4. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```
5. Configure o banco no **.env** (veja [Variáveis de Ambiente](#-variáveis-de-ambiente-env)):
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1` (ou o host do seu MySQL)
   - `DB_PORT=3306`
   - `DB_DATABASE=plataforma` (ou outro nome; crie o banco no MySQL)
   - `DB_USERNAME=root` (ou seu usuário)
   - `DB_PASSWORD=sua_senha`
6. Rode as migrações e o seed:
   ```bash
   php artisan migrate --seed
   ```
7. Inicie o servidor:
   ```bash
   php artisan serve
   ```
   O backend ficará em **http://127.0.0.1:8000**.

Se o frontend rodar em outra porta ou domínio, ajuste `FRONTEND_URL` no `.env` do backend (usado em redirecionamentos e CORS/Sanctum).

---

## 🖥️ Frontend: Configuração e Execução

O frontend fica na pasta **`frontend/`**. Documentação completa em **[frontend/README.md](frontend/README.md)**.

### Execução em desenvolvimento

1. Entre na pasta do frontend:
   ```bash
   cd frontend
   ```
2. Instale as dependências:
   ```bash
   npm install
   ```
3. Inicie o servidor de desenvolvimento:
   ```bash
   npm run dev
   ```
   A aplicação abrirá em **http://localhost:5173** (porta configurável no `vite.config.js`).

Em modo dev, o **Vite** faz proxy para o backend (veja `frontend/vite.config.js`):

- `/api` → `http://127.0.0.1:8000`
- `/sanctum` → `http://127.0.0.1:8000`
- `/login`, `/register`, `/logout` → `http://127.0.0.1:8000`

Assim, não é obrigatório definir **VITE_API_URL** no frontend em desenvolvimento: a baseURL do Axios pode ficar vazia e as requisições relativas (ex.: `/api/v1/user`) serão enviadas ao mesmo host (localhost:5173), e o Vite repassa para a porta 8000. Os cookies de sessão do Sanctum funcionam porque o proxy mantém o mesmo “origin” da perspectiva do navegador.

### Variável de ambiente do frontend (opcional em dev)

- **VITE_API_URL** – URL base da API. Em desenvolvimento com proxy, pode ficar vazia ou não definida. Em produção, se a SPA for servida de outro domínio, defina aqui a URL completa da API (ex.: `https://api.seudominio.com`). Crie um arquivo **`frontend/.env`** ou **`frontend/.env.production`** conforme necessário:
  ```env
  VITE_API_URL=http://127.0.0.1:8000
  ```
  Só variáveis com prefixo `VITE_` são expostas ao código pelo Vite.

### Build para produção

```bash
cd frontend
npm run build
```

A saída fica em **`frontend/dist/`**. Esses arquivos podem ser servidos pelo Laravel (copiando para `backend/public/` e configurando rotas), por Nginx ou por outro servidor. Em produção, configure o backend (CORS, Sanctum stateful domains) para o domínio onde a SPA será servida e, se necessário, defina **VITE_API_URL** no build.

---

## 🗄️ Banco de Dados (MySQL)

- **Motor:** MySQL 8.x (ou MariaDB compatível). No Docker usa a imagem `mysql:8.0`.
- **Nome do banco:** No Docker é **`plataforma`** (definido em `MYSQL_DATABASE` e `DB_DATABASE`). Localmente pode usar o mesmo nome ou outro (ex.: `laravel`); crie o banco manualmente e preencha `DB_DATABASE` no `.env` do backend.
- **Tabelas:** Todas são criadas pelas **migrations** do Laravel em `backend/database/migrations/`. Ordem lógica: `users` (com coluna `role`), `areas`, `teachers`, `students`, `courses` (foreign key para `areas`), `disciplines` (para `courses` e `teachers`), `enrollments` (pivot entre `students` e `courses`), além das tabelas padrão (sessions, cache, failed_jobs, personal_access_tokens, etc.).
- **Seed:** O **DevelopmentSeeder** (chamado por `DatabaseSeeder`) cria:
  - Um usuário **admin**: `admin@plataforma.test` / senha `password`.
  - Um usuário **aluno** (e registro em `students`): `emanuel@plataforma.test` / senha `password`.
  - Áreas (Biologia, Física, Química, Matemática), cursos, professores, disciplinas e matrículas de exemplo.

Após `php artisan migrate --seed` (ou o entrypoint no Docker), o sistema já pode ser usado com as credenciais acima.

---

## 🐳 Docker: Serviços e Scripts

O arquivo **`docker-compose.yml`** na raiz define dois serviços:

| Serviço | Imagem / Build | Porta | Descrição |
|--------|----------------|-------|-----------|
| **app** | Build: `./backend` (Dockerfile) | 8000 | Aplicação Laravel. Depende do MySQL estar saudável. Variáveis de ambiente (APP_*, DB_*) definidas no compose. |
| **mysql** | mysql:8.0 | 3306 | Banco MySQL. Volume `mysql_data` persiste os dados. Healthcheck para o app só subir quando o MySQL aceitar conexão. |

**Variáveis do app no compose:**  
`APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_CONNECTION`, `DB_HOST=mysql`, `DB_PORT`, `DB_DATABASE=plataforma`, `DB_USERNAME=root`, `DB_PASSWORD=secret`.

O **backend** usa ainda o **docker-entrypoint.sh**, que:

1. Espera o MySQL ficar acessível (PDO).
2. Gera `APP_KEY` se estiver vazio.
3. Executa `php artisan migrate --force` e `php artisan db:seed --force`.
4. Inicia o comando passado (por padrão `php artisan serve --host=0.0.0.0 --port=8000`).

**Scripts de conveniência:**

- **run.ps1** (Windows): Verifica Docker, roda `docker compose up -d --build`, aguarda alguns segundos, verifica se os containers estão up e se o app responde em http://127.0.0.1:8000; exibe as credenciais e comandos para parar/ver logs.
- **run.sh** (Linux/macOS): Equivalente em shell.

Comandos úteis:

```bash
docker compose up -d --build   # Subir em background
docker compose down           # Parar e remover containers
docker compose logs -f app    # Logs do backend
docker compose logs -f mysql  # Logs do MySQL
```

---

## 🔐 Variáveis de Ambiente (.env)

### Backend (`backend/.env`)

Copie de **`backend/.env.example`**. As principais para rodar a aplicação:

| Variável | Descrição | Exemplo (local) | Exemplo (Docker) |
|----------|-----------|------------------|------------------|
| **APP_NAME** | Nome da aplicação | Laravel | Plataforma Prof. Jubilut |
| **APP_ENV** | Ambiente | local | local |
| **APP_KEY** | Chave de criptografia | (vazio; rodar `php artisan key:generate`) | (gerado no entrypoint) |
| **APP_DEBUG** | Modo debug | true | true |
| **APP_URL** | URL do backend | http://localhost:8000 | http://localhost:8000 |
| **FRONTEND_URL** | URL do frontend (redirecionamentos/CORS) | http://localhost:5173 | http://localhost:5173 |
| **DB_CONNECTION** | Driver do banco | mysql | mysql |
| **DB_HOST** | Host do MySQL | 127.0.0.1 | mysql |
| **DB_PORT** | Porta do MySQL | 3306 | 3306 |
| **DB_DATABASE** | Nome do banco | plataforma ou laravel | plataforma |
| **DB_USERNAME** | Usuário MySQL | root | root |
| **DB_PASSWORD** | Senha MySQL | (sua senha) | secret |

**SESSION_DRIVER** e **SESSION_LIFETIME** são usados para sessão web; para API com Sanctum stateful, o domínio do frontend deve estar em **SANCTUM_STATEFUL_DOMAINS** (no `config/sanctum.php` o padrão já inclui localhost:5173 e 127.0.0.1:8000). **CORS** é configurado em `config/cors.php` e pode usar `FRONTEND_URL` se necessário.

No Docker, o backend pode usar o arquivo **`backend/.env.docker`** como referência (DB_HOST=mysql, DB_DATABASE=plataforma, DB_PASSWORD=secret); o entrypoint copia para `.env` dentro do container se existir.

### Frontend (`frontend/.env`)

Opcional. A única variável usada pelo código (em `src/api/axios.js`) é:

| Variável | Descrição | Desenvolvimento | Produção |
|----------|-----------|------------------|----------|
| **VITE_API_URL** | URL base da API | Vazio (proxy Vite) | URL completa da API (ex.: https://api.exemplo.com) |

Arquivos suportados: `.env`, `.env.local`, `.env.development`, `.env.production`. Sempre com prefixo **VITE_** para serem expostos ao build.

---

## 👤 Credenciais Padrão

Criadas pelo **DevelopmentSeeder** após `php artisan db:seed` (ou ao subir o Docker):

| Papel | E-mail | Senha |
|-------|--------|--------|
| **Administrador** | admin@plataforma.test | password |
| **Aluno** | emanuel@plataforma.test | password |

Use a **interface em http://localhost:5173** (frontend) para fazer login. O admin é redirecionado para `/admin/dashboard` e o aluno para `/aluno/dashboard`.

---

## 📂 Estrutura do Repositório

```
TesteInfityworksPhp/
├── README.md                 # Este arquivo (visão geral, como rodar, .env, Docker)
├── docker-compose.yml       # Serviços app (Laravel) + mysql
├── run.ps1                  # Script Windows: sobe Docker e valida
├── run.sh                   # Script Linux/macOS: idem
├── backend/                 # API Laravel 10
│   ├── README.md            # Documentação detalhada do backend
│   ├── .env.example         # Modelo de .env
│   ├── .env.docker          # Referência para ambiente Docker
│   ├── Dockerfile           # Imagem PHP 8.2 + Composer + app
│   ├── docker-entrypoint.sh # Espera MySQL, migrate, seed, serve
│   ├── app/                 # Models, Controllers, Services, Middleware, etc.
│   ├── config/              # Configurações (database, auth, sanctum, cors, etc.)
│   ├── database/            # migrations, seeders, factories
│   ├── routes/              # web.php, api.php
│   └── ...
└── frontend/                # SPA Vue 3
    ├── README.md            # Documentação detalhada do frontend
    ├── index.html
    ├── vite.config.js       # Proxy para o backend em dev
    ├── package.json
    ├── src/
    │   ├── main.js
    │   ├── App.vue
    │   ├── api/             # axios.js
    │   ├── stores/          # auth (Pinia)
    │   ├── router/          # Rotas e guard
    │   ├── services/       # AuthService, AreaService, etc.
    │   ├── layouts/         # AuthLayout, AdminLayout, AlunoLayout
    │   ├── components/      # ui (AppButton, AppInput, AppCard), charts (Bar, Pie, etc.)
    │   └── pages/           # auth, admin, aluno
    └── ...
```

---

## 📚 Documentação Detalhada

- **[backend/README.md](backend/README.md)** – Visão geral do backend, arquitetura, modelagem do banco, autenticação e papéis (admin/aluno), fluxo de requisições, componentes (Models, Controllers, Services, Repositories, Form Requests, API Resources), endpoints da API, serviços e regras de negócio, testes, tecnologias e execução local/Docker.
- **[backend/docs/ROLES-E-AUTH.md](backend/docs/ROLES-E-AUTH.md)** – Explicação de como o sistema separa admin e aluno (cadastro, login, rotas da API e do frontend).
- **[frontend/README.md](frontend/README.md)** – Visão geral do frontend, arquitetura da SPA, fluxo de autenticação e navegação, estrutura de pastas, API client e interceptors, store Pinia (auth), serviços (camada de API), layouts e rotas, componentes de UI e gráficos, páginas por área, tecnologias, variáveis de ambiente e build.

---

## 🔧 Solução de Problemas

### Backend não responde após subir o Docker

- Confira se o container está rodando: `docker compose ps`.
- Veja os logs: `docker compose logs -f app`. O entrypoint espera o MySQL; se o MySQL demorar, o app pode ainda estar em “Aguardando MySQL…”.
- Verifique o healthcheck do MySQL: `docker compose logs mysql`. Se o MySQL não ficar healthy, o app não inicia.

### Erro de conexão com o banco (local)

- Confirme que o MySQL está rodando e que `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` no `backend/.env` estão corretos.
- Crie o banco manualmente se não existir: `CREATE DATABASE plataforma;` (ou o nome que colocou em `DB_DATABASE`).

### Frontend não consegue fazer login ou chama API e dá 401/404

- Em desenvolvimento, o backend deve estar acessível em **http://127.0.0.1:8000** e o Vite em **http://localhost:5173**. O proxy em `frontend/vite.config.js` aponta para `http://127.0.0.1:8000`. Se o backend estiver em outra porta, altere o `target` do proxy.
- Se estiver usando produção (build estático), defina **VITE_API_URL** no build e configure **CORS** e **SANCTUM_STATEFUL_DOMAINS** no backend para o domínio da SPA.
- Para login com sessão, o frontend deve chamar primeiro `GET /sanctum/csrf-cookie` (AuthService.getCsrfCookie()) e depois POST /login com `withCredentials: true` (já configurado no axios).

### “Acesso não autorizado” ou redirecionamento para login ao acessar /admin ou /aluno

- O guard do router e o backend verificam o papel. Se o usuário logado for aluno, não pode acessar rotas de admin e vice-versa. Faça logout e entre com a conta correta (admin@plataforma.test ou emanuel@plataforma.test).

### Limpar tudo e recomeçar (Docker)

```bash
docker compose down -v   # Remove containers e volumes (apaga dados do MySQL)
docker compose up -d --build
```

Agora você tem o README geral com visão do projeto, backend, frontend, como rodar (Docker e local), .env, banco de dados, Docker e links para a documentação detalhada de cada parte.
