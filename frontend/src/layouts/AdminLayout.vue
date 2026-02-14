<template>
  <div class="flex min-h-screen bg-slate-50">
    <!-- Overlay no mobile quando menu aberto -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-black/40 lg:hidden"
      aria-hidden="true"
      @click="sidebarOpen = false"
    />
    <aside
      class="fixed inset-y-0 left-0 z-40 w-56 border-r border-slate-200 bg-white shadow-sm transition-transform duration-200 lg:w-64 lg:translate-x-0"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="flex h-14 items-center border-b border-slate-200 px-4">
        <router-link to="/admin/dashboard" class="text-lg font-semibold text-slate-800" @click="onNavClick">
          Plataforma Jubilut
        </router-link>
      </div>
      <nav class="flex flex-col gap-1 overflow-y-auto p-3">
        <router-link
          v-for="link in adminLinks"
          :key="link.to"
          :to="link.to"
          active-class="bg-blue-50 text-blue-700"
          class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
          @click="onNavClick"
        >
          <component :is="link.icon" class="h-4 w-4 shrink-0" />
          {{ link.label }}
        </router-link>
      </nav>
    </aside>
    <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
      <header class="sticky top-0 z-20 flex h-14 shrink-0 items-center justify-between gap-2 border-b border-slate-200 bg-white px-3 shadow-sm sm:px-4">
        <div class="flex min-w-0 items-center gap-2">
          <button
            type="button"
            class="flex shrink-0 rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
            aria-label="Abrir menu"
            @click="sidebarOpen = true"
          >
            <Menu class="h-5 w-5" />
          </button>
          <h2 class="truncate text-base font-semibold text-slate-800 sm:text-lg">{{ currentTitle }}</h2>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          <span class="hidden text-sm text-slate-600 sm:inline">{{ authStore.userName }}</span>
          <button
            type="button"
            class="rounded-lg px-2 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100 sm:px-3"
            @click="logout"
          >
            Sair
          </button>
        </div>
      </header>
      <main class="min-w-0 flex-1 overflow-x-hidden p-3 sm:p-4 md:p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import {
  LayoutDashboard,
  FolderTree,
  BookOpen,
  Users,
  GraduationCap,
  UserPlus,
  ClipboardList,
  BarChart3,
  UserCog,
  Menu,
} from 'lucide-vue-next';
import { ref } from 'vue';

const route = useRoute();
const sidebarOpen = ref(false);

function onNavClick() {
  if (window.innerWidth < 1024) sidebarOpen.value = false;
}
const router = useRouter();
const authStore = useAuthStore();

const adminLinks = [
  { to: '/admin/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/admin/areas', label: 'Áreas', icon: FolderTree },
  { to: '/admin/courses', label: 'Cursos', icon: BookOpen },
  { to: '/admin/teachers', label: 'Professores', icon: Users },
  { to: '/admin/disciplines', label: 'Disciplinas', icon: GraduationCap },
  { to: '/admin/students', label: 'Alunos', icon: UserPlus },
  { to: '/admin/enrollments', label: 'Matrículas', icon: ClipboardList },
  { to: '/admin/reports', label: 'Relatórios', icon: BarChart3 },
  { to: '/admin/register', label: 'Cadastrar administrador', icon: UserCog },
];

const currentTitle = computed(() => {
  const t = route.meta.title;
  if (t) return t;
  const name = route.name?.toString() ?? '';
  if (name.includes('dashboard')) return 'Dashboard';
  if (name.includes('areas')) return 'Áreas';
  if (name.includes('courses')) return 'Cursos';
  if (name.includes('teachers')) return 'Professores';
  if (name.includes('disciplines')) return 'Disciplinas';
  if (name.includes('students')) return 'Alunos';
  if (name.includes('enrollments')) return 'Matrículas';
  if (name.includes('reports')) return 'Relatórios';
  if (name.includes('register')) return 'Cadastrar administrador';
  return 'Admin';
});

async function logout() {
  await authStore.logout();
  router.push('/login');
}
</script>
