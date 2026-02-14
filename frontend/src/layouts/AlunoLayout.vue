<template>
  <div class="flex min-h-screen flex-col bg-slate-50">
    <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm">
      <router-link to="/aluno/dashboard" class="text-lg font-semibold text-slate-800">
        Plataforma Jubilut
      </router-link>
      <nav class="flex items-center gap-2">
        <router-link
          to="/aluno/dashboard"
          active-class="text-blue-600"
          class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100"
        >
          Dashboard
        </router-link>
        <router-link
          to="/aluno/profile"
          active-class="text-blue-600"
          class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100"
        >
          Editar cadastro
        </router-link>
        <span class="text-sm text-slate-500">{{ authStore.userName }}</span>
        <button
          type="button"
          class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
          @click="logout"
        >
          Sair
        </button>
      </nav>
    </header>
    <main class="flex-1 p-4 sm:p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

async function logout() {
  await authStore.logout();
  router.push('/login');
}
</script>
