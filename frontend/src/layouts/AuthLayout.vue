<template>
  <div class="flex min-h-screen flex-col items-center justify-center bg-slate-100 px-4 py-8">
    <div class="w-full max-w-md">
      <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-slate-800">Plataforma Prof. Jubilut</h1>
        <p class="mt-1 text-sm text-slate-500">Entre ou cadastre-se</p>
      </div>
      <main class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <router-view />
      </main>
      <p class="mt-4 text-center text-xs text-slate-500">
        &copy; Plataforma Prof. Jubilut
      </p>
    </div>
  </div>
</template>

<script setup>
</script>
