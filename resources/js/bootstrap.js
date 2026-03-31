import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Importar utilidades de accesibilidad
import { AccessibilityManager, accessibilityAlpine } from './utils/accessibility.js';

// Importar utilidades de performance (comentado temporalmente para evitar errores)
// import { PerformanceOptimizer, performanceAlpine } from './utils/performance.js';

// Exponer utilidades globalmente
window.AccessibilityManager = AccessibilityManager;
window.accessibilityAlpine = accessibilityAlpine;
// window.PerformanceOptimizer = PerformanceOptimizer;
// window.performanceAlpine = performanceAlpine;

/**
 * Echo exposure for Reverb
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Configurar Echo solo si hay configuración disponible
if (window.laravelConfig?.reverb?.appKey || import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: window.laravelConfig?.reverb?.appKey ?? import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: window.laravelConfig?.reverb?.host ?? import.meta.env.VITE_REVERB_HOST,
        wsPort: (window.laravelConfig?.reverb?.port ?? import.meta.env.VITE_REVERB_PORT ?? 80),
        wssPort: (window.laravelConfig?.reverb?.port ?? import.meta.env.VITE_REVERB_PORT ?? 443),
        forceTLS: (window.laravelConfig?.reverb?.scheme ?? import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    console.warn('Reverb configuration not found. WebSocket connections disabled.');
    window.Echo = {
        // Echo mock para evitar errores
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