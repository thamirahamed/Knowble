import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#1b8f67"
    },
});

// Access the Pusher credentials via process.env
window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: '8b3c3cd9313f2c8cdd03', // Using the Pusher key from the environment
    cluster: 'ap2', // Using the Pusher cluster from the environment
    forceTLS: true,
});
