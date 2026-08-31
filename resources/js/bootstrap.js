import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Importar utilidades de accesibilidad
import { AccessibilityManager, accessibilityAlpine } from './utils/accessibility.js';

// Exponer utilidades globalmente
window.AccessibilityManager = AccessibilityManager;
window.accessibilityAlpine = accessibilityAlpine;

/**
 * Echo exposure for Ably Realtime WebSockets
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const rawAblyKey = window.laravelConfig?.ably?.key ?? import.meta.env.VITE_ABLY_KEY;

if (rawAblyKey) {
    const publicAblyKey = rawAblyKey.includes(':') ? rawAblyKey.split(':')[0] : rawAblyKey;
    const startEcho = () => {
        if (window.Echo) return;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: publicAblyKey,
            wsHost: 'realtime-pusher.ably.io',
            wsPort: 443,
            wssPort: 443,
            forceTLS: true,
            disableStats: true,
            cluster: 'mt1',
            enabledTransports: ['ws', 'wss'],
        });
    };
    if ('requestIdleCallback' in window) {
        requestIdleCallback(startEcho, { timeout: 2500 });
    } else {
        setTimeout(startEcho, 1500);
    }
} else {
    // Ably optional in public views
    window.Echo = {
        channel: () => ({ listen: () => ({}) }),
        private: () => ({ listen: () => ({}) }),
        join: () => ({ listen: () => ({}) }),
        leave: () => {},
        socketId: () => null,
    };
}

// Importar y configurar Alpine.js
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// Configurar Alpine.js
window.Alpine = Alpine;
Alpine.plugin(focus);

// Iniciar Alpine.js cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    
});