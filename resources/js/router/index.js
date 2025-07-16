import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import Borrowers from '../components/Borrowers.vue';
import RoomsAppoint from '../components/RoomsAppoint.vue';

const routes = [
  { path: '/', name: 'Dashboard', component: Dashboard },
  { path: '/borrowers', name: 'Borrowers', component: Borrowers },
  { path: '/appointment', name: 'RoomsAppoint', component: RoomsAppoint},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
