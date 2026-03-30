import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposure for Reverb
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.laravelConfig?.reverb?.appKey ?? import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.laravelConfig?.reverb?.host ?? import.meta.env.VITE_REVERB_HOST,
    wsPort: (window.laravelConfig?.reverb?.port ?? import.meta.env.VITE_REVERB_PORT ?? 80),
    wssPort: (window.laravelConfig?.reverb?.port ?? import.meta.env.VITE_REVERB_PORT ?? 443),
    forceTLS: (window.laravelConfig?.reverb?.scheme ?? import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});