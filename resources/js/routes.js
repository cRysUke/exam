import dashboard from './components/dashboard.vue'
import notFound from './components/notFound.vue'
import products from './components/products/index.vue'
import user from './components/user/index.vue'


export default [
    {
        path: '/',

        name: 'admin.dashboard',

        component: dashboard,
    },
    {
        path: '/products',

        name: 'admin.products',

        component: products,
    },
    {
        path: '/user',

        name: 'admin.user',

        component: user,
    },
    {
        path: '/:pathMatch(.*)*',

        name: 'notFound',

        component: notFound,
    },
]
