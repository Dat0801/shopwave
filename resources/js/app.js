import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

createInertiaApp({
    title: (title) => {
        let siteName = 'Laravel';
        const appElement = document.getElementById('app');
        if (appElement && appElement.dataset.page) {
            try {
                const page = JSON.parse(appElement.dataset.page);
                if (page?.props?.settings?.site_name) {
                    siteName = page.props.settings.site_name;
                }
            } catch (e) {
                // Keep default
            }
        }
        return `${title} - ${siteName}`;
    },
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
