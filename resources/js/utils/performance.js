// resources/js/utils/performance.js
// Optimizaciones para PageSpeed Insights >95

export class PerformanceOptimizer {
    constructor() {
        this.initLazyLoading();
        this.initResourceHints();
        this.initImageOptimization();
        this.initConnectionAwareLoading();
        this.initPerformanceMonitoring();
    }

    /**
     * Lazy loading inteligente con Intersection Observer
     */
    initLazyLoading() {
        // Configurar Intersection Observer
        const observerOptions = {
            rootMargin: '50px',
            threshold: 0.1,
            root: null
        };

        // Observer para imágenes
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Cargar imagen
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    // Cargar srcset
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-srcset');
                    }
                    
                    // Manejar eventos de carga
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                        PerformanceOptimizer.logPerformance('image-loaded', {
                            src: img.src,
                            loadTime: performance.now()
                        });
                    });
                    
                    img.addEventListener('error', () => {
                        // Fallback a placeholder
                        if (img.dataset.fallback) {
                            img.src = img.dataset.fallback;
                        }
                    });
                    
                    imageObserver.unobserve(img);
                }
            });
        }, observerOptions);

        // Observer para iframes y embeds
        const iframeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                        iframeObserver.unobserve(iframe);
                    }
                }
            });
        }, observerOptions);

        // Aplicar observers
        document.addEventListener('DOMContentLoaded', () => {
            // Imágenes
            document.querySelectorAll('img[data-src], img[data-srcset]').forEach(img => {
                imageObserver.observe(img);
            });

            // Iframes
            document.querySelectorAll('iframe[data-src]').forEach(iframe => {
                iframeObserver.observe(iframe);
            });

            // Background images
            document.querySelectorAll('[data-bg]').forEach(el => {
                const bgObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            el.style.backgroundImage = `url(${el.dataset.bg})`;
                            el.removeAttribute('data-bg');
                            bgObserver.unobserve(el);
                        }
                    });
                }, observerOptions);
                bgObserver.observe(el);
            });
        });
    }

    /**
     * Preconexión y precarga de recursos críticos
     */
    initResourceHints() {
        // Agregar resource hints dinámicamente
        const hints = [
            // Preconectar a dominios externos críticos
            // { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
            // { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: true },
            
            // Precargar fuentes críticas (comentado temporalmente)
            // { rel: 'preload', href: '/assets/fonts/inter-var.woff2', as: 'font', type: 'font/woff2', crossorigin: true },
            
            // Precargar CSS crítico (ya inline)
            // { rel: 'preload', href: '/assets/app.css', as: 'style' },
            
            // Precargar JS crítico (comentado temporalmente)
            // { rel: 'preload', href: '/assets/app.js', as: 'script' },
        ];

        hints.forEach(hint => {
            const link = document.createElement('link');
            Object.keys(hint).forEach(key => {
                if (hint[key]) {
                    link.setAttribute(key, hint[key]);
                }
            });
            document.head.appendChild(link);
        });

        // Precargar imágenes above-the-fold
        const criticalImages = document.querySelectorAll('img[data-critical]');
        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.dataset.src || img.src;
            document.head.appendChild(link);
        });
    }

    /**
     * Optimización de imágenes
     */
    initImageOptimization() {
        // Detectar soporte para formatos modernos
        const supportsWebP = () => {
            const canvas = document.createElement('canvas');
            if (canvas.getContext && canvas.getContext('2d')) {
                return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
            }
            return false;
        };

        const supportsAvif = async () => {
            const avif = new Image();
            avif.src = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgANogQEAwgMg8f8D///8WfhwB8+ErK42A=';
            return new Promise(resolve => {
                avif.onload = () => resolve(true);
                avif.onerror = () => resolve(false);
            });
        };

        // Actualizar imágenes para usar formatos modernos
        document.addEventListener('DOMContentLoaded', async () => {
            const isWebPSupported = supportsWebP();
            const isAvifSupported = await supportsAvif();

            document.querySelectorAll('img[data-src], img[src]').forEach(img => {
                const currentSrc = img.dataset.src || img.src;
                
                if (currentSrc) {
                    // Para imágenes que ya están optimizadas, no hacer nada
                    if (currentSrc.includes('.webp') || currentSrc.includes('.avif')) {
                        return;
                    }

                    // Actualizar a formato óptimo
                    let optimizedSrc = currentSrc;
                    
                    if (isAvifSupported) {
                        optimizedSrc = currentSrc.replace(/\.(jpg|jpeg|png)$/i, '.avif');
                    } else if (isWebPSupported) {
                        optimizedSrc = currentSrc.replace(/\.(jpg|jpeg|png)$/i, '.webp');
                    }

                    // Actualizar src si es diferente
                    if (optimizedSrc !== currentSrc) {
                        if (img.dataset.src) {
                            img.dataset.src = optimizedSrc;
                        } else {
                            img.src = optimizedSrc;
                        }
                        
                        // Agregar fallback
                        img.onerror = function() {
                            this.src = currentSrc;
                            this.onerror = null;
                        };
                    }
                }
            });
        });
    }

    /**
     * Carga consciente de la conexión
     */
    initConnectionAwareLoading() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            // Ajustar calidad de imágenes basado en conexión
            const adjustImageQuality = () => {
                const effectiveType = connection.effectiveType;
                const saveData = connection.saveData;
                
                document.querySelectorAll('img[data-src-quality]').forEach(img => {
                    const quality = img.dataset.srcQuality;
                    let targetQuality = 'high';
                    
                    if (saveData || effectiveType === 'slow-2g' || effectiveType === '2g') {
                        targetQuality = 'low';
                    } else if (effectiveType === '3g') {
                        targetQuality = 'medium';
                    }
                    
                    if (quality !== targetQuality) {
                        const currentSrc = img.dataset.src || img.src;
                        const newSrc = currentSrc.replace(/(low|medium|high)/, targetQuality);
                        
                        if (img.dataset.src) {
                            img.dataset.src = newSrc;
                        } else {
                            img.src = newSrc;
                        }
                        
                        img.dataset.srcQuality = targetQuality;
                    }
                });
            };

            // Escuchar cambios en la conexión
            if (connection.addEventListener) {
                connection.addEventListener('change', adjustImageQuality);
            }
            
            adjustImageQuality();
        }

        // Defer cargas no críticas en conexiones lentas
        if (navigator.connection && (navigator.connection.saveData || navigator.connection.effectiveType.includes('2g'))) {
            document.addEventListener('DOMContentLoaded', () => {
                // Retrasar cargas no críticas
                const nonCritical = document.querySelectorAll('[data-load-delayed]');
                nonCritical.forEach(el => {
                    setTimeout(() => {
                        if (el.dataset.src) {
                            el.src = el.dataset.src;
                            el.removeAttribute('data-src');
                        }
                    }, 3000);
                });
            });
        }
    }

    /**
     * Monitoreo de performance
     */
    initPerformanceMonitoring() {
        // Registrar métricas de Core Web Vitals
        if (typeof window.webVitals === 'function') {
            window.webVitals.getCLS(console.log);
            window.webVitals.getFID(console.log);
            window.webVitals.getLCP(console.log);
            window.webVitals.getFCP(console.log);
            window.webVitals.getTTFB(console.log);
        }

        // Monitorear recursos lentos
        const resources = performance.getEntriesByType('resource');
        const slowResources = resources.filter(r => r.duration > 1000);
        
        if (slowResources.length > 0) {
            console.warn('Recursos lentos detectados:', slowResources);
            PerformanceOptimizer.logPerformance('slow-resources', slowResources);
        }

        // Monitorear Largest Contentful Paint
        let lcpValue = 0;
        const lcpObserver = new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            lcpValue = lastEntry.renderTime || lastEntry.loadTime;
            
            PerformanceOptimizer.logPerformance('lcp', {
                value: lcpValue,
                element: lastEntry.element?.tagName,
                url: lastEntry.element?.src || lastEntry.element?.href
            });
        });

        lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });

        // Monitorear Cumulative Layout Shift
        let clsValue = 0;
        const clsObserver = new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            
            PerformanceOptimizer.logPerformance('cls', { value: clsValue });
        });

        clsObserver.observe({ type: 'layout-shift', buffered: true });
    }

    /**
     * Log de métricas de performance
     */
    static logPerformance(metric, data) {
        const logData = {
            metric,
            data,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent
        };

        // Enviar a analytics si está configurado
        // Comentado temporalmente para evitar errores 404
        // if (window.axios) {
        //     window.axios.post('/api/performance-metrics', logData).catch(() => {
        //         // Fallback a console
        //         console.log('Performance Metric:', logData);
        //     });
        // } else {
        //     console.log('Performance Metric:', logData);
        // }
        console.log('Performance Metric:', logData);
    }

    /**
     * Optimizar animaciones para performance
     */
    static optimizeAnimations() {
        // Usar transform y opacity para animaciones de performance
        document.querySelectorAll('[x-transition], [x-show]').forEach(el => {
            if (el.__x) {
                // Asegurar que Alpine use propiedades de performance
                let originalShow = el.__x.$data.show;
                Object.defineProperty(el.__x.$data, 'show', {
                    get() {
                        return originalShow;
                    },
                    set(value) {
                        if (value !== originalShow) {
                            // Forzar composición de GPU
                            el.style.willChange = 'transform, opacity';
                            originalShow = value;
                            
                            // Limpiar después de la animación
                            setTimeout(() => {
                                el.style.willChange = 'auto';
                            }, 300);
                        }
                    }
                });
            }
        });
    }

    /**
     * Prefetch de rutas probables
     */
    static prefetchLikelyRoutes() {
        // Prefetch de enlaces visibles (comentado temporalmente)
        // const observer = new IntersectionObserver((entries) => {
        //     entries.forEach(entry => {
        //         if (entry.isIntersecting) {
        //             const link = entry.target;
        //             if (link.href && link.href.startsWith(window.location.origin)) {
        //                 const prefetchLink = document.createElement('link');
        //                 prefetchLink.rel = 'prefetch';
        //                 prefetchLink.href = link.href;
        //                 prefetchLink.as = 'document';
        //                 document.head.appendChild(prefetchLink);
        //             }
        //             observer.unobserve(link);
        //         }
        //     });
        // }, { rootMargin: '200px' });

        // Observar enlaces en el viewport (comentado temporalmente)
        // document.querySelectorAll('a[href^="/"]').forEach(link => {
        //     observer.observe(link);
        // });
    }
}

// Inicializar automáticamente
document.addEventListener('DOMContentLoaded', () => {
    // Comentado temporalmente para evitar errores
    // const optimizer = new PerformanceOptimizer();
    
    // Optimizar animaciones después de que Alpine se inicialice
    // setTimeout(() => {
    //     PerformanceOptimizer.optimizeAnimations();
    //     PerformanceOptimizer.prefetchLikelyRoutes();
    // }, 1000);
    
    // Exponer globalmente
    // window.PerformanceOptimizer = PerformanceOptimizer;
    // window.performanceOptimizer = optimizer;
});

// Utilidades para Alpine.js
export const performanceAlpine = {
    // Directiva para lazy loading de componentes
    lazy: {
        mounted(el, binding) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Cargar componente cuando sea visible
                        if (binding.value && typeof binding.value === 'function') {
                            binding.value();
                        }
                        observer.unobserve(el);
                    }
                });
            }, { rootMargin: '100px' });
            
            observer.observe(el);
            el.__performance_observer = observer;
        },
        
        unmounted(el) {
            if (el.__performance_observer) {
                el.__performance_observer.disconnect();
            }
        }
    },
    
    // Directiva para conexión consciente
    connectionAware: {
        mounted(el, binding) {
            const connection = navigator.connection;
            
            if (connection) {
                const update = () => {
                    const effectiveType = connection.effectiveType;
                    const saveData = connection.saveData;
                    
                    if (binding.value && typeof binding.value === 'function') {
                        binding.value({ effectiveType, saveData });
                    }
                };
                
                if (connection.addEventListener) {
                    connection.addEventListener('change', update);
                }
                
                update();
                el.__connection_update = update;
            }
        },
        
        unmounted(el) {
            const connection = navigator.connection;
            if (connection && connection.removeEventListener && el.__connection_update) {
                connection.removeEventListener('change', el.__connection_update);
            }
        }
    }
};