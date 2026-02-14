# Frontend – Plataforma Prof. Jubilut

Este diretório (**`C:\Projetos\TesteInfityworksPhp\frontend`**) é o **único frontend** da aplicação (SPA em Vue 3).

## Onde fica o quê

| Local | Conteúdo |
|-------|----------|
| **`frontend/`** (aqui) | Aplicação SPA: Vue 3, Vue Router, Pinia, ApexCharts, Tailwind. Consome a API do backend. |
| **`backend/app`** | Apenas código PHP (controllers, models, services). **Não há frontend aqui.** |
| **`backend/resources/views`** | Views Blade (opcional; podem ser mantidas para fallback ou removidas se usar só SPA). |

Não é necessário “mover” frontend de `backend\app` para `frontend`: em `backend\app` não existe interface; a interface fica neste projeto (`frontend/`).

## Desenvolvimento

```bash
cd frontend
npm install
npm run dev
```

Acesse: **http://localhost:5173**. O Vite faz proxy de `/api`, `/login`, `/register`, `/logout` e `/sanctum` para o backend (ex.: `http://127.0.0.1:8000`).

## Build

```bash
npm run build
```

A saída fica em `frontend/dist/`. Em produção, o Laravel pode servir esses arquivos ou um outro servidor (ex.: Nginx) pode servir o `frontend` e fazer proxy da API para o backend.
