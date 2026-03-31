// resources/js/utils/accessibility.js
// Utilidades de accesibilidad ADA/WCAG para Laravel + Alpine.js

export class AccessibilityManager {
    constructor() {
        this.initSkipLinks();
        this.initFocusTraps();
        this.initLiveRegions();
        this.initKeyboardNavigation();
        this.initReducedMotion();
    }

    /**
     * Crear skip links dinámicamente para navegación con teclado
     */
    initSkipLinks() {
        const skipLinks = [
            { target: '#main-content', text: 'Saltar al contenido principal' },
            { target: '#navigation', text: 'Saltar a la navegación' },
            { target: '#search-form', text: 'Saltar al formulario de búsqueda' },
            { target: '#footer', text: 'Saltar al pie de página' }
        ];

        skipLinks.forEach(link => {
            if (document.querySelector(link.target)) {
                const skipLink = document.createElement('a');
                skipLink.href = link.target;
                skipLink.className = 'skip-link sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 focus:z-50 focus:bg-primary focus:text-white focus:px-4 focus:py-2';
                skipLink.textContent = link.text;
                skipLink.setAttribute('tabindex', '0');
                document.body.prepend(skipLink);
            }
        });
    }

    /**
     * Inicializar trampas de foco para modales
     */
    initFocusTraps() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                // Cerrar modales con Escape
                const openModals = document.querySelectorAll('[x-show*="modalOpen"]');
                openModals.forEach(modal => {
                    if (modal.__x && modal.__x.$data && modal.__x.$data.modalOpen) {
                        modal.__x.$data.modalOpen = false;
                    }
                });
            }

            // Trap focus dentro de modales
            if (e.key === 'Tab') {
                const activeModal = document.querySelector('[x-show*="modalOpen"][x-show="true"]');
                if (activeModal) {
                    const focusableElements = activeModal.querySelectorAll(
                        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                    );
                    
                    if (focusableElements.length === 0) return;

                    const firstElement = focusableElements[0];
                    const lastElement = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey && document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    } else if (!e.shiftKey && document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }

    /**
     * Crear regiones live para actualizaciones dinámicas
     */
    initLiveRegions() {
        // Región para notificaciones
        const notificationRegion = document.createElement('div');
        notificationRegion.setAttribute('aria-live', 'polite');
        notificationRegion.setAttribute('aria-atomic', 'true');
        notificationRegion.className = 'sr-only';
        notificationRegion.id = 'live-region-notifications';
        document.body.appendChild(notificationRegion);

        // Región para alertas
        const alertRegion = document.createElement('div');
        alertRegion.setAttribute('aria-live', 'assertive');
        alertRegion.setAttribute('aria-atomic', 'true');
        alertRegion.className = 'sr-only';
        alertRegion.id = 'live-region-alerts';
        document.body.appendChild(alertRegion);
    }

    /**
     * Mejorar navegación con teclado
     */
    initKeyboardNavigation() {
        // Navegación por teclado en menús
        document.addEventListener('keydown', (e) => {
            const menu = e.target.closest('[role="menu"], [role="navigation"]');
            
            if (menu) {
                const items = menu.querySelectorAll('[role="menuitem"], [role="menuitemradio"], [role="menuitemcheckbox"], a, button');
                const currentIndex = Array.from(items).indexOf(document.activeElement);
                
                switch(e.key) {
                    case 'ArrowDown':
                    case 'ArrowRight':
                        e.preventDefault();
                        const nextIndex = (currentIndex + 1) % items.length;
                        items[nextIndex].focus();
                        break;
                        
                    case 'ArrowUp':
                    case 'ArrowLeft':
                        e.preventDefault();
                        const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                        items[prevIndex].focus();
                        break;
                        
                    case 'Home':
                        e.preventDefault();
                        items[0].focus();
                        break;
                        
                    case 'End':
                        e.preventDefault();
                        items[items.length - 1].focus();
                        break;
                        
                    case 'Enter':
                    case ' ':
                        if (document.activeElement.tagName === 'A' || document.activeElement.tagName === 'BUTTON') {
                            e.preventDefault();
                            document.activeElement.click();
                        }
                        break;
                }
            }
        });

        // Mejorar navegación en formularios
        document.addEventListener('keydown', (e) => {
            if (e.target.matches('input, select, textarea')) {
                if (e.key === 'Enter' && !e.target.matches('textarea')) {
                    // Buscar siguiente campo en formulario
                    const form = e.target.closest('form');
                    if (form) {
                        const formElements = form.querySelectorAll('input, select, textarea, button');
                        const currentIndex = Array.from(formElements).indexOf(e.target);
                        const nextElement = formElements[currentIndex + 1];
                        
                        if (nextElement) {
                            e.preventDefault();
                            nextElement.focus();
                        }
                    }
                }
            }
        });
    }

    /**
     * Respeta preferencias de movimiento reducido
     */
    initReducedMotion() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        if (prefersReducedMotion.matches) {
            document.documentElement.classList.add('reduced-motion');
            
            // Deshabilitar animaciones CSS
            const style = document.createElement('style');
            style.textContent = `
                .reduced-motion *,
                .reduced-motion *::before,
                .reduced-motion *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                    scroll-behavior: auto !important;
                }
            `;
            document.head.appendChild(style);
        }

        // Escuchar cambios en la preferencia
        prefersReducedMotion.addEventListener('change', (e) => {
            if (e.matches) {
                document.documentElement.classList.add('reduced-motion');
            } else {
                document.documentElement.classList.remove('reduced-motion');
            }
        });
    }

    /**
     * Anunciar cambios dinámicos a screen readers
     */
    static announce(message, priority = 'polite') {
        const regionId = priority === 'assertive' ? 'live-region-alerts' : 'live-region-notifications';
        const region = document.getElementById(regionId);
        
        if (region) {
            region.textContent = '';
            setTimeout(() => {
                region.textContent = message;
            }, 100);
        }
    }

    /**
     * Mejorar etiquetas ARIA para elementos dinámicos
     */
    static enhanceAriaLabels() {
        // Mejorar botones sin texto
        document.querySelectorAll('button:not([aria-label]):empty, button:not([aria-label]) img').forEach(button => {
            const icon = button.querySelector('img, svg');
            if (icon) {
                const alt = icon.getAttribute('alt') || icon.getAttribute('aria-label');
                if (alt) {
                    button.setAttribute('aria-label', alt);
                }
            }
        });

        // Mejorar enlaces que abren en nueva ventana
        document.querySelectorAll('a[target="_blank"]:not([rel="noopener"]):not([rel="noreferrer"])').forEach(link => {
            link.setAttribute('rel', 'noopener noreferrer');
            
            if (!link.getAttribute('aria-label') && !link.querySelector('img, svg')) {
                const currentLabel = link.textContent.trim();
                if (currentLabel) {
                    link.setAttribute('aria-label', `${currentLabel} (se abre en nueva ventana)`);
                }
            }
        });

        // Mejorar formularios
        document.querySelectorAll('form').forEach(form => {
            if (!form.id) {
                form.id = `form-${Math.random().toString(36).substr(2, 9)}`;
            }
            
            const legend = form.querySelector('legend');
            if (legend && !form.getAttribute('aria-labelledby')) {
                form.setAttribute('aria-labelledby', legend.id || (legend.id = `legend-${Math.random().toString(36).substr(2, 9)}`));
            }
        });
    }

    /**
     * Validar contraste de colores
     */
    static validateColorContrast(element = document.body) {
        const elements = element.querySelectorAll('*');
        const lowContrastElements = [];
        
        elements.forEach(el => {
            const style = window.getComputedStyle(el);
            const bgColor = style.backgroundColor;
            const textColor = style.color;
            
            // Implementación básica de validación de contraste
            // Nota: Para producción, usar una librería como `color-contrast-checker`
            if (bgColor !== 'rgba(0, 0, 0, 0)' && textColor !== 'rgba(0, 0, 0, 0)') {
                console.log(`Contraste para ${el.tagName}:`, { bgColor, textColor });
            }
        });
        
        return lowContrastElements;
    }
}

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const accessibility = new AccessibilityManager();
    AccessibilityManager.enhanceAriaLabels();
    
    // Exponer globalmente para uso en Alpine.js
    window.AccessibilityManager = AccessibilityManager;
    window.accessibility = accessibility;
});

// Utilidades para Alpine.js
export const accessibilityAlpine = {
    // Directiva para mejorar accesibilidad en elementos interactivos
    accessible: {
        mounted(el, binding) {
            const role = binding.value?.role || 'button';
            const label = binding.value?.label || el.textContent.trim() || el.getAttribute('aria-label');
            
            if (!el.getAttribute('role')) {
                el.setAttribute('role', role);
            }
            
            if (!el.getAttribute('aria-label') && label) {
                el.setAttribute('aria-label', label);
            }
            
            if (!el.getAttribute('tabindex') && el.tagName !== 'BUTTON' && el.tagName !== 'A' && el.tagName !== 'INPUT') {
                el.setAttribute('tabindex', '0');
            }
        }
    },
    
    // Directiva para anunciar cambios a screen readers
    announce: {
        updated(el, binding) {
            if (binding.value !== binding.oldValue) {
                AccessibilityManager.announce(binding.value, binding.modifiers.assertive ? 'assertive' : 'polite');
            }
        }
    },
    
    // Directiva para trampa de foco en modales
    focusTrap: {
        mounted(el) {
            el.__accessibility_focusTrap = true;
        },
        
        unmounted(el) {
            el.__accessibility_focusTrap = false;
        }
    }
};