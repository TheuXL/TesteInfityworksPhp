# Instalação do Backend (Laravel)

## Passo a passo completo (o que ainda falta fazer)

**Importante:** Todos os comandos abaixo devem ser executados **dentro da pasta `backend`**. O arquivo `artisan` fica em `backend`, não na raiz do projeto.

| # | O que fazer | Comando (no terminal) |
|---|-------------|------------------------|
| 1 | Entrar na pasta do backend | `cd backend` |
| 2 | Instalar dependências (se ainda não tiver a pasta `vendor`) | `composer install --no-interaction --ignore-platform-reqs` |
| 3 | Copiar o arquivo de ambiente (se ainda não tiver `.env`) | `copy .env.example .env` |
| 4 | Gerar a chave da aplicação | `php artisan key:generate` |
| 5 | Configurar o banco no `.env` | Editar `.env`: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (ou usar SQLite – veja abaixo) |
| 6 | Criar as tabelas e popular dados de teste | `php artisan migrate --seed` |
| 7 | Subir o servidor (opcional) | `php artisan serve` |
| 8 | Acessar no navegador | http://127.0.0.1:8000 |

**Credenciais após o seed:**
- **Admin:** `admin@plataforma.test` / senha: `password`
- **Aluno:** `emanuel@plataforma.test` / senha: `password`

**Exemplo de sequência no PowerShell (a partir da raiz do projeto):**
```powershell
cd C:\Projetos\TesteInfityworksPhp\backend
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**Se quiser usar SQLite (sem instalar MySQL):** no `.env` use:
```env
DB_CONNECTION=sqlite
DB_DATABASE="C:\Projetos\TesteInfityworksPhp\backend\database\database.sqlite"
```
Antes de migrar, crie o arquivo vazio: `New-Item -Path database\database.sqlite -ItemType File`

---

## Erro: `vendor/autoload.php` não encontrado

Esse erro indica que as dependências do Composer ainda não foram instaladas. Siga um dos caminhos abaixo.

---

## Opção 1: Habilitar extensões PHP (recomendado)

O Composer precisa das extensões **fileinfo**, **curl** e **zip** para baixar os pacotes com mais rapidez.

1. Abra o arquivo `php.ini` usado pelo PHP (o caminho aparece na mensagem do Composer ou ao rodar `php --ini`).
   - Exemplo no Windows: `C:\Users\...\WinGet\Packages\PHP.PHP.8.3_...\php.ini`

2. Remova o `;` do início das linhas para **habilitar** as extensões:
   ```ini
   extension=fileinfo
   extension=curl
   extension=zip
   ```

3. Salve o arquivo e execute no diretório do backend:
   ```bash
   cd backend
   composer install
   ```

---

## Opção 2: Instalar ignorando requisitos de plataforma

Se não puder habilitar as extensões agora, use:

```bash
cd backend
composer install --no-interaction --ignore-platform-reqs
```

**Atenção:** A instalação pode demorar vários minutos (o Composer baixa os pacotes via Git em vez de ZIP). Aguarde até aparecer a mensagem de conclusão.

---

## Depois de instalar o `vendor`

1. Copie o ambiente e gere a chave:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

2. Configure o banco no `.env` (MySQL ou SQLite para testes):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=plataforma_jubilut
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Rode as migrações e o seed:
   ```bash
   php artisan migrate --seed
   ```

4. Acesse a aplicação (por exemplo `php artisan serve`) e use:
   - **Admin:** `admin@plataforma.test` / `password`
   - **Aluno:** `emanuel@plataforma.test` / `password`

---

## Testes (PHPUnit)

Na pasta `backend`, com dependências instaladas e PHP com extensões (mbstring, dom, xml, etc.):

```bash
cd backend
php vendor/bin/phpunit
```

O `phpunit.xml` usa SQLite em memória (`DB_DATABASE=:memory:`), então não é necessário MySQL para rodar os testes.
