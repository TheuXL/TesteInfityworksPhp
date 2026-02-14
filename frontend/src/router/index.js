import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import AuthLayout from '../layouts/AuthLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import AlunoLayout from '../layouts/AlunoLayout.vue';

const routes = [
  {
    path: '/',
    redirect: (to) => {
      const auth = useAuthStore();
      if (auth.user) {
        return auth.isAdmin ? '/admin/dashboard' : '/aluno/dashboard';
      }
      return '/login';
    },
  },
  {
    path: '/login',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'login', component: () => import('../pages/auth/Login.vue') },
    ],
  },
  {
    path: '/register',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      { path: '', name: 'register', component: () => import('../pages/auth/Register.vue') },
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      { path: 'dashboard', name: 'admin.dashboard', component: () => import('../pages/admin/Dashboard.vue') },
      { path: 'areas', name: 'admin.areas', component: () => import('../pages/admin/areas/AreaList.vue') },
      { path: 'areas/create', name: 'admin.areas.create', component: () => import('../pages/admin/areas/AreaForm.vue'), meta: { formMode: 'create' } },
      { path: 'areas/:id/edit', name: 'admin.areas.edit', component: () => import('../pages/admin/areas/AreaForm.vue'), meta: { formMode: 'edit' } },
      { path: 'courses', name: 'admin.courses', component: () => import('../pages/admin/courses/CourseList.vue') },
      { path: 'courses/create', name: 'admin.courses.create', component: () => import('../pages/admin/courses/CourseForm.vue'), meta: { formMode: 'create' } },
      { path: 'courses/:id/edit', name: 'admin.courses.edit', component: () => import('../pages/admin/courses/CourseForm.vue'), meta: { formMode: 'edit' } },
      { path: 'teachers', name: 'admin.teachers', component: () => import('../pages/admin/teachers/TeacherList.vue') },
      { path: 'teachers/create', name: 'admin.teachers.create', component: () => import('../pages/admin/teachers/TeacherForm.vue'), meta: { formMode: 'create' } },
      { path: 'teachers/:id/edit', name: 'admin.teachers.edit', component: () => import('../pages/admin/teachers/TeacherForm.vue'), meta: { formMode: 'edit' } },
      { path: 'disciplines', name: 'admin.disciplines', component: () => import('../pages/admin/disciplines/DisciplineList.vue') },
      { path: 'disciplines/create', name: 'admin.disciplines.create', component: () => import('../pages/admin/disciplines/DisciplineForm.vue'), meta: { formMode: 'create' } },
      { path: 'disciplines/:id/edit', name: 'admin.disciplines.edit', component: () => import('../pages/admin/disciplines/DisciplineForm.vue'), meta: { formMode: 'edit' } },
      { path: 'students', name: 'admin.students', component: () => import('../pages/admin/students/StudentList.vue') },
      { path: 'students/create', name: 'admin.students.create', component: () => import('../pages/admin/students/StudentForm.vue'), meta: { formMode: 'create' } },
      { path: 'students/:id/edit', name: 'admin.students.edit', component: () => import('../pages/admin/students/StudentForm.vue'), meta: { formMode: 'edit' } },
      { path: 'enrollments', name: 'admin.enrollments', component: () => import('../pages/admin/enrollments/EnrollmentList.vue') },
      { path: 'enrollments/create', name: 'admin.enrollments.create', component: () => import('../pages/admin/enrollments/EnrollmentForm.vue'), meta: { formMode: 'create' } },
      { path: 'enrollments/:id/edit', name: 'admin.enrollments.edit', component: () => import('../pages/admin/enrollments/EnrollmentForm.vue'), meta: { formMode: 'edit' } },
      { path: 'reports', name: 'admin.reports', component: () => import('../pages/admin/Reports.vue') },
      { path: 'register', name: 'admin.register', component: () => import('../pages/admin/RegisterAdmin.vue') },
    ],
  },
  {
    path: '/aluno',
    component: AlunoLayout,
    meta: { requiresAuth: true, role: 'student' },
    children: [
      { path: 'dashboard', name: 'aluno.dashboard', component: () => import('../pages/aluno/Dashboard.vue') },
      { path: 'profile', name: 'aluno.profile', component: () => import('../pages/aluno/ProfileEdit.vue') },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();
  if (!auth.userFetched) {
    await auth.fetchUser();
  }

  if (to.meta.guest && auth.user) {
    return next(auth.isAdmin ? '/admin/dashboard' : '/aluno/dashboard');
  }
  if (to.meta.requiresAuth && !auth.user) {
    return next({ path: '/login', query: { redirect: to.fullPath } });
  }
  if (to.meta.role && auth.user?.role !== to.meta.role) {
    return next(auth.isAdmin ? '/admin/dashboard' : '/aluno/dashboard');
  }
  next();
});

export default router;
