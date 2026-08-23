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
    // Si la clave tiene formato 'keyId:secret', extraemos solo la parte pública
    const publicAblyKey = rawAblyKey.includes(':') ? rawAblyKey.split(':')[0] : rawAblyKey;

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
} else {
    console.warn('Ably configuration not found. Realtime connections disabled.');
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
    console.log('Alpine.js started successfully');
});