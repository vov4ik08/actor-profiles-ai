import { createRouter, createWebHistory } from 'vue-router';

import SubmitFormPage from './views/SubmitFormPage.vue';
import ActorsTablePage from './views/ActorsTablePage.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'submit', component: SubmitFormPage },
        { path: '/actors', name: 'actors', component: ActorsTablePage },
    ],
});

export default router;

