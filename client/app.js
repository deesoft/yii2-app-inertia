import vuetify from '@/plugins/vuetify';
import mainPlugin from '@/plugins/main';
import DefaultLayout from '@/layouts/Default.vue';
import { createInertiaApp } from '@inertiajs/vue3';

import 'vuetify/lib/styles/main.sass';
import '@/assets/css/app.css';

function setDefaultLayout(page) {
    if (page.default && typeof page.default.layout === 'undefined') {
        page.default.layout = DefaultLayout;
    }
    return page;
}


const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
function resolvePageComponent(name) {
    const path = `./pages/${name}.vue`;
    const page = pages[path];
    if (typeof page === 'undefined') {
        throw new Error(`Page not found: ${path}`);
    }
    if (typeof page === 'function') {
        return page().then(page => setDefaultLayout(page));
    } else {
        return setDefaultLayout(page);
    }
}
createInertiaApp({
    resolve: resolvePageComponent,
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(mainPlugin)
            .use(vuetify)
            .mount(el)
    },
});