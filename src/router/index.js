import Vue from 'vue';
import VueRouter from 'vue-router';
import { generateUrl } from '@nextcloud/router';

import ExportExcelContent from '../views/ExportExcelContent.vue';

const routes = [
	{
		name: 'all',
		path: '/all',
		component: ExportExcelContent,
	},
];

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes,
	base: generateUrl('/apps/accountexport/'),
	linkActiveClass: 'active',
});

export default router;
