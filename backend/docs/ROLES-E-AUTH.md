# Como o sistema separa Admin e Aluno

**Resposta direta:** o sistema diferencia o cadastro pelo **lugar** em que ele é feito:
- **Cadastro de aluno:** formulário público em `/register` → sempre cria usuário com `role = student`.
- **Cadastro de admin:** formulário dentro da área admin em `/admin/register` → só um admin logado acessa; cria usuário com `role = admin`. O primeiro admin vem do seed (ex.: admin@plataforma.test) ou do banco.

## 1. No banco de dados (backend)

- A tabela **`users`** tem a coluna **`role`** com valores:
  - `admin` → administrador
  - `student` → aluno
- O enum **`App\Enums\UserRole`** define esses valores.
- **Alunos** têm um registro relacionado na tabela **`students`** (1:1 com `users`), usado para perfil, matrículas, etc.
- **Admins** não têm registro em `students`.

## 2. No backend (Laravel)

- **Registro aluno:** `POST /register` (público) — cria `role = student` e registro em `students`; não faz login automático.
- **Registro admin:** `POST /api/v1/admin/register` (requer admin logado) — cria `role = admin`; só outro admin pode chamar.
- **Login:** uma única tela (`POST /login`); o backend redireciona para `/admin/dashboard` ou `/aluno/dashboard` conforme o `role`.
- **Rotas da API:**
  - **`/api/v1/admin/*`** → middleware **`role.admin`**: só usuários com `role === admin`.
  - **`/api/v1/aluno/*`** → middleware **`role.student`**: só usuários com `role === student`.
- **`/api/v1/user`** → retorna o usuário logado (com `role` e, se for aluno, `student`). Qualquer usuário autenticado pode acessar.

## 3. No frontend (Vue)

- O **auth store** guarda o objeto do usuário (vindo de login ou de `/api/v1/user`).
- **`user.role`** é usado nos getters:
  - **`isAdmin`** → `user?.role === 'admin'`
  - **`isStudent`** → `user?.role === 'student'`
- O **router** usa essas flags e `meta` das rotas:
  - Rotas com **`meta: { guest: true }`** (login/register): se já estiver logado, redireciona para dashboard (admin ou aluno conforme o role).
  - Rotas com **`meta: { requiresAuth: true, role: 'admin' }`** (`/admin/*`): exige login e `role === 'admin'`.
  - Rotas com **`meta: { requiresAuth: true, role: 'student' }`** (`/aluno/*`): exige login e `role === 'student'`.
- Assim, **admin** só acessa `/admin/*` e **aluno** só acessa `/aluno/*`; o guard redireciona se o role não bater.

## 4. Resumo

| Quem        | Onde é definido | Onde é usado (backend)     | Onde é usado (frontend)   |
|------------|------------------|----------------------------|----------------------------|
| **Admin**  | `users.role = admin` | `role.admin` nas rotas `/api/v1/admin/*` | `auth.isAdmin`, rotas `/admin/*` |
| **Aluno**  | `users.role = student` + tabela `students` | `role.student` nas rotas `/api/v1/aluno/*` | `auth.isStudent`, rotas `/aluno/*` |

**Como cadastrar um admin:** (1) Faça login como um admin já existente (ex.: seed `admin@plataforma.test` / `password`). (2) No menu lateral da área admin, clique em **"Cadastrar administrador"** (`/admin/register`). (3) Preencha nome, e-mail e senha do novo admin e envie. O novo usuário já pode entrar na mesma tela de login (`/login`) com esse e-mail e senha.
