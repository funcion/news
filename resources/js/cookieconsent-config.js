import 'vanilla-cookieconsent/dist/cookieconsent.css';
import * as CookieConsent from 'vanilla-cookieconsent';

// Initialize Vanilla CookieConsent v3
CookieConsent.run({
    guiOptions: {
        consentModal: {
            layout: 'box',
            position: 'bottom right',
            equalWeightButtons: true,
            flipButtons: false
        },
        preferencesModal: {
            layout: 'box',
            position: 'right',
            equalWeightButtons: true,
            flipButtons: false
        }
    },
    categories: {
        necessary: {
            readOnly: true,
            enabled: true
        },
        analytics: {
            autoClear: {
                cookies: [
                    {
                        name: /^(_ga|_gid|_gat)/
                    }
                ]
            }
        },
        ads: {}
    },
    language: {
        default: document.documentElement.lang === 'es' ? 'es' : 'en',
        autoDetect: 'document',
        translations: {
            es: {
                consentModal: {
                    title: 'Gestionamos tu Privacidad 🍪',
                    description: 'En Glodaxia utilizamos cookies técnicas esenciales y analíticas anónimas para optimizar tu experiencia y recordar tus preferencias. Puedes aceptar todas, rechazarlas o personalizar tus preferencias.',
                    acceptAllBtn: 'Aceptar Todas',
                    acceptNecessaryBtn: 'Solo Esenciales',
                    showPreferencesBtn: 'Gestionar Preferencias',
                    footer: '<a href="/es/privacy">Política de Privacidad</a>\n<a href="/es/cookies">Política de Cookies</a>'
                },
                preferencesModal: {
                    title: 'Centro de Preferencias de Privacidad',
                    acceptAllBtn: 'Aceptar Todas',
                    acceptNecessaryBtn: 'Rechazar Opcionales',
                    savePreferencesBtn: 'Guardar Preferencias',
                    closeIconLabel: 'Cerrar',
                    serviceCounterLabel: 'Servicio|Servicios',
                    sections: [
                        {
                            title: 'Uso de Cookies en Glodaxia',
                            description: 'Utilizamos cookies para garantizar las funciones básicas de la web y mejorar tu experiencia. Puedes configurar cada categoría según tus preferencias.'
                        },
                        {
                            title: 'Cookies Estrictamente Necesarias <span class="pm__badge">Siempre Activas</span>',
                            description: 'Estas cookies son imprescindibles para la navegación, seguridad CSRF y persistencia de tu Modo Claro u Oscuro.',
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Cookies de Rendimiento y Analítica',
                            description: 'Nos permiten recopilar estadísticas de lectura totalmente anónimas para entender qué contenidos son más relevantes y mejorar la calidad editorial.',
                            linkedCategory: 'analytics'
                        },
                        {
                            title: 'Cookies de Publicidad y Personalización',
                            description: 'Permiten ofrecer contenidos y avisos relevantes según tus intereses generales.',
                            linkedCategory: 'ads'
                        },
                        {
                            title: 'Más Información',
                            description: 'Para cualquier consulta sobre nuestra política de privacidad, visita <a class="cc__link" href="/es/privacy">nuestra Política de Privacidad</a>.'
                        }
                    ]
                }
            },
            en: {
                consentModal: {
                    title: 'We Value Your Privacy 🍪',
                    description: 'Glodaxia uses essential technical cookies and anonymous analytics to ensure platform performance and remember your visual preferences. You can accept all, reject non-essential, or customize your preferences.',
                    acceptAllBtn: 'Accept All',
                    acceptNecessaryBtn: 'Essential Only',
                    showPreferencesBtn: 'Manage Preferences',
                    footer: '<a href="/privacy">Privacy Policy</a>\n<a href="/cookies">Cookie Policy</a>'
                },
                preferencesModal: {
                    title: 'Privacy Preference Center',
                    acceptAllBtn: 'Accept All',
                    acceptNecessaryBtn: 'Reject All',
                    savePreferencesBtn: 'Save Preferences',
                    closeIconLabel: 'Close',
                    serviceCounterLabel: 'Service|Services',
                    sections: [
                        {
                            title: 'Cookie Usage at Glodaxia',
                            description: 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. You can choose your preferences for each category.'
                        },
                        {
                            title: 'Strictly Necessary Cookies <span class="pm__badge">Always Active</span>',
                            description: 'These cookies are essential for website operation (session security, CSRF protection, and Dark/Light Mode state).',
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Performance & Analytics Cookies',
                            description: 'Help us measure readership anonymously to elevate editorial quality and site stability.',
                            linkedCategory: 'analytics'
                        },
                        {
                            title: 'Advertisement & Targeting Cookies',
                            description: 'Used to provide relevant technical content and disclosures aligned with your interests.',
                            linkedCategory: 'ads'
                        },
                        {
                            title: 'More Information',
                            description: 'For any queries regarding cookies, please visit our <a class="cc__link" href="/privacy">Privacy Policy</a>.'
                        }
                    ]
                }
            }
        }
    }
});

export default CookieConsent;