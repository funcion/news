# Documentación de Diseño UI/UX - Sistema de Noticias

## 1. Principios Fundamentales de Diseño

### 1.1. Filosofía de Desarrollo
- **Nunca usar código hardcodeado**: Todas las configuraciones, textos y valores deben ser dinámicos
- **Componentes reutilizables**: Cada elemento debe ser independiente y reutilizable
- **CSS modular**: Cada sección tiene sus propios estilos CSS
- **Procesamiento con Vite**: Todo CSS y JavaScript se procesa a través de Vite
- **Accesibilidad total**: Cumplir con ADA, WCAG 2.1 AA
- **Performance óptima**: Puntuación >95 en PageSpeed Insights

### 1.2. Arquitectura de Componentes (Stack Laravel/Blade/Alpine.js)
```
resources/
├── views/
│   ├── components/          # Componentes Blade reutilizables
│   │   ├── layout/          # Layout components
│   │   ├── ui/              # UI components (buttons, cards)
│   │   └── partials/        # Partial components
│   ├── layouts/             # Layout templates
│   └── pages/               # Page templates
├── css/
│   ├── base/                # Estilos base y reset
│   ├── components/          # Estilos por componente
│   ├── utilities/           # Clases utilitarias
│   └── themes/              # Temas y variables
└── js/
    ├── components/          # Componentes Alpine.js
    ├── utils/               # Utilidades JavaScript
    └── vendors/             # Dependencias externas
```

## 2. Sistema de CSS Modular con Vite

### 2.1. Configuración de Vite
```javascript
// vite.config.js
export default {
  css: {
    modules: {
      localsConvention: 'camelCase',
      generateScopedName: '[name]__[local]___[hash:base64:5]'
    },
    postcss: {
      plugins: [
        require('autoprefixer'),
        require('tailwindcss')
      ]
    }
  },
  build: {
    cssCodeSplit: true,
    rollupOptions: {
      output: {
        assetFileNames: 'assets/[name]-[hash][extname]',
        chunkFileNames: 'assets/[name]-[hash].js',
        entryFileNames: 'assets/[name]-[hash].js'
      }
    }
  }
}
```

### 2.2. Estructura de Estilos por Sección
```css
/* styles/components/header.css */
.header {
  --header-bg: var(--color-primary);
  --header-text: var(--color-white);
  
  background-color: var(--header-bg);
  color: var(--header-text);
  padding: var(--spacing-md);
}

.header__logo {
  width: var(--logo-width);
  height: var(--logo-height);
}

/* styles/components/article-card.css */
.article-card {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
  --card-radius: var(--border-radius-md);
  
  border-radius: var(--card-radius);
  box-shadow: var(--card-shadow);
  transition: transform 0.3s ease;
}

.article-card:hover {
  transform: translateY(-4px);
}
```

## 3. Mejores Prácticas de Implementación

### 3.1. Variables CSS Dinámicas
```css
/* styles/base/variables.css */
:root {
  /* Colores dinámicos - nunca hardcodeados */
  --color-primary: rgb(var(--primary-r, 59), var(--primary-g, 130), var(--primary-b, 246));
  --color-secondary: rgb(var(--secondary-r, 107), var(--secondary-g, 114), var(--secondary-b, 128));
  
  /* Espaciado responsive */
  --spacing-xs: clamp(0.25rem, 0.5vw, 0.5rem);
  --spacing-sm: clamp(0.5rem, 1vw, 1rem);
  --spacing-md: clamp(1rem, 2vw, 1.5rem);
  --spacing-lg: clamp(1.5rem, 3vw, 2rem);
  
  /* Tipografía responsive */
  --font-size-sm: clamp(0.875rem, 1.5vw, 1rem);
  --font-size-md: clamp(1rem, 2vw, 1.25rem);
  --font-size-lg: clamp(1.25rem, 3vw, 1.5rem);
}
```

### 3.2. Componentes Blade Dinámicos sin Hardcode
```blade
{{-- resources/views/components/ui/article-card.blade.php --}}
@props([
    'article',
    'maxTitleLength' => 100,
    'showExcerpt' => true,
    'excerptLength' => 150,
    'imageAspectRatio' => '16/9',
    'config' => []
])

@php
    // Configuración dinámica desde props o contexto
    $config = array_merge([
        'maxTitleLength' => $maxTitleLength,
        'showExcerpt' => $showExcerpt,
        'excerptLength' => $excerptLength,
        'imageAspectRatio' => $imageAspectRatio,
    ], $config);
    
    $titleId = 'article-title-' . $article->id;
    $truncatedTitle = strlen($article->title) > $config['maxTitleLength'] 
        ? substr($article->title, 0, $config['maxTitleLength']) . '...'
        : $article->title;
    
    $truncatedExcerpt = $config['showExcerpt'] && $article->excerpt
        ? (strlen($article->excerpt) > $config['excerptLength'] 
            ? substr($article->excerpt, 0, $config['excerptLength']) . '...'
            : $article->excerpt)
        : null;
@endphp

<article 
    class="article-card"
    aria-labelledby="{{ $titleId }}"
    style="--aspect-ratio: {{ $config['imageAspectRatio'] }}"
    x-data="{ isLoaded: false }"
>
    <div class="article-card__image-container">
        <img
            src="{{ $article->image }}"
            alt="{{ $article->alt_text ?? 'Imagen de ' . $article->title }}"
            loading="lazy"
            @load="isLoaded = true"
            :class="{ 'article-card__image--loaded': isLoaded }"
            class="article-card__image"
        />
    </div>
    
    <div class="article-card__content">
        <h3 
            id="{{ $titleId }}"
            class="article-card__title"
        >
            {{ $truncatedTitle }}
        </h3>
        
        @if($truncatedExcerpt)
            <p class="article-card__excerpt">
                {{ $truncatedExcerpt }}
            </p>
        @endif
        
        <div class="article-card__meta">
            <time datetime="{{ $article->published_at->toIso8601String() }}">
                {{ $article->published_at->format('d/m/Y') }}
            </time>
            <span class="article-card__category">
                {{ $article->category->name }}
            </span>
        </div>
    </div>
</article>
```

## 4. Accesibilidad ADA/WCAG 2.1 AA (Implementado)

### 4.1. Sistema de Accesibilidad Implementado

**Archivos implementados:**
- `resources/js/utils/accessibility.js` - Gestor completo de accesibilidad
- `resources/css/base/reset.css` - Reset con accesibilidad integrada
- `resources/css/base/variables.css` - Variables CSS con soporte para alto contraste

### 4.2. Características Implementadas

#### ✅ **Skip Links Dinámicos**
- Links para saltar al contenido principal, navegación, búsqueda y footer
- Solo visibles con navegación por teclado
- Implementado automáticamente en `AccessibilityManager.initSkipLinks()`

#### ✅ **Trampas de Foco para Modales**
- Foco atrapado dentro de modales abiertos
- Cierre con tecla Escape
- Navegación cíclica con Tab/Shift+Tab

#### ✅ **Regiones Live para Screen Readers**
- Región `aria-live="polite"` para notificaciones
- Región `aria-live="assertive"` para alertas críticas
- Anuncios automáticos con `AccessibilityManager.announce()`

#### ✅ **Navegación por Teclado Mejorada**
- Navegación con flechas en menús
- Atajos Home/End para listas
- Mejora de navegación en formularios

#### ✅ **Soporte para Movimiento Reducido**
- Detección automática de `prefers-reduced-motion`
- Desactivación de animaciones cuando se solicita
- Clase `.reduced-motion` aplicada automáticamente

#### ✅ **Mejora Automática de ARIA Labels**
- Botones sin texto reciben labels automáticos
- Enlaces que abren en nueva ventana anunciados apropiadamente
- Formularios etiquetados correctamente

### 4.3. Directivas Alpine.js para Accesibilidad

```javascript
// Uso en componentes Alpine.js
<div 
  x-data="{ modalOpen: false }"
  x-accessibility.accessible="{ role: 'dialog', label: 'Modal de configuración' }"
  x-accessibility.announce="'Modal abierto'"
>
  <!-- Contenido del modal -->
</div>
```

### 4.2. JavaScript Accesible
```javascript
// scripts/accessibility.js
export class AccessibilityManager {
  constructor() {
    this.initSkipLinks();
    this.initFocusTraps();
    this.initLiveRegions();
  }

  initSkipLinks() {
    // Crear skip links dinámicamente
    const skipLinks = [
      { target: '#main-content', text: 'Saltar al contenido principal' },
      { target: '#navigation', text: 'Saltar a la navegación' },
      { target: '#search', text: 'Saltar a la búsqueda' }
    ];

    skipLinks.forEach(link => {
      const skipLink = document.createElement('a');
      skipLink.href = link.target;
      skipLink.className = 'skip-link';
      skipLink.textContent = link.text;
      skipLink.setAttribute('tabindex', '0');
      document.body.prepend(skipLink);
    });
  }

  initLiveRegions() {
    // Crear regiones live para actualizaciones dinámicas
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    document.body.appendChild(liveRegion);
  }
}
```

## 5. Optimización para PageSpeed Insights >95 (Implementado)

### 5.1. Sistema de Performance Implementado

**Archivos implementados:**
- `resources/js/utils/performance.js` - Optimizador completo de performance
- `resources/css/utilities/performance.css` - Utilidades CSS para performance
- `vite.config.js` - Configuración optimizada para Laravel

### 5.2. Estrategias de Performance Implementadas

#### ✅ **Lazy Loading Inteligente**
- Imágenes con `data-src` y `data-srcset`
- Iframes y embeds diferidos
- Background images con lazy loading
- Intersection Observer con umbral configurable

#### ✅ **Resource Hints Dinámicos**
- Preconexión a dominios críticos
- Precarga de fuentes y assets
- Prefetch de rutas probables
- Implementado en `PerformanceOptimizer.initResourceHints()`

#### ✅ **Optimización de Imágenes**
- Detección automática de soporte WebP/AVIF
- Conversión automática a formatos modernos
- Fallback a formatos tradicionales
- Calidad ajustada según conexión

#### ✅ **Carga Consciente de la Conexión**
- Ajuste de calidad según `navigator.connection`
- Soporte para `saveData` mode
- Defer de cargas no críticas en conexiones lentas
- Escucha de cambios en la conexión

#### ✅ **Monitoreo de Core Web Vitals**
- Tracking de LCP (Largest Contentful Paint)
- Monitoreo de CLS (Cumulative Layout Shift)
- Detección de recursos lentos
- Logging automático a analytics

#### ✅ **Optimización de Animaciones**
- Uso de transform/opacity para animaciones GPU
- `will-change` aplicado estratégicamente
- Respeto a `prefers-reduced-motion`
- Directivas Alpine.js optimizadas

### 5.3. Utilidades CSS para Performance

```css
/* Ejemplos de utilidades implementadas */
.img-optimized { image-rendering: crisp-edges; }
.critical-content { content-visibility: auto; }
.animate-gpu { transform: translateZ(0); will-change: transform; }
.hover-performance { transition: transform 0.2s cubic-bezier(...); }
.layout-optimized { contain: layout style paint; }
```

### 5.4. Directivas Alpine.js para Performance

```javascript
// Lazy loading de componentes
<div x-performance.lazy="loadComponent">
  <!-- Componente cargado cuando sea visible -->
</div>

// Conexión consciente
<div x-performance.connectionAware="adjustQuality">
  <!-- Calidad ajustada según conexión -->
</div>
```

### 5.5. Métricas Objetivo (PageSpeed Insights)

| Métrica | Objetivo | Estado |
|---------|----------|--------|
| **Performance** | >95 | ✅ Implementado |
| **Accessibility** | 100 | ✅ Implementado |
| **Best Practices** | 100 | ✅ Implementado |
| **SEO** | 100 | ✅ Implementado |
| **LCP** | <2.5s | ✅ Monitoreado |
| **FID** | <100ms | ✅ Optimizado |
| **CLS** | <0.1 | ✅ Controlado |

### 5.2. CSS Crítico Inline
```html
<!DOCTYPE html>
<html lang="es">
<head>
  <style>
    /* CSS crítico inline - máximo 14KB */
    :root {
      --color-primary: #3b82f6;
      --color-text: #1f2937;
      --font-family: system-ui, -apple-system, sans-serif;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: var(--font-family);
      color: var(--color-text);
      line-height: 1.5;
    }
    
    /* Solo estilos necesarios para above-the-fold */
    .header,
    .hero,
    .main-nav {
      opacity: 1;
      transform: translateY(0);
      transition: opacity 0.3s, transform 0.3s;
    }
  </style>
  
  <!-- CSS no crítico cargado asíncrono -->
  <link 
    rel="preload" 
    href="/assets/non-critical.css" 
    as="style" 
    onload="this.onload=null;this.rel='stylesheet'"
  >
  <noscript><link rel="stylesheet" href="/assets/non-critical.css"></noscript>
</head>
```

### 5.3. Lazy Loading Inteligente
```javascript
// scripts/lazy-loading.js
export class LazyLoader {
  constructor() {
    this.observer = new IntersectionObserver(
      this.handleIntersection.bind(this),
      {
        rootMargin: '50px',
        threshold: 0.1
      }
    );
    
    this.lazyImages = document.querySelectorAll('[data-src]');
    this.lazyBackgrounds = document.querySelectorAll('[data-bg]');
    
    this.init();
  }

  init() {
    this.lazyImages.forEach(img => this.observer.observe(img));
    this.lazyBackgrounds.forEach(el => this.observer.observe(el));
  }

  handleIntersection(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = entry.target;
        
        if (target.dataset.src) {
          target.src = target.dataset.src;
          target.removeAttribute('data-src');
        }
        
        if (target.dataset.bg) {
          target.style.backgroundImage = `url(${target.dataset.bg})`;
          target.removeAttribute('data-bg');
        }
        
        this.observer.unobserve(target);
      }
    });
  }
}
```

## 6. Sistema de Configuración Dinámica

### 6.1. Configuración Centralizada
```javascript
// config/theme.js
export const themeConfig = {
  colors: {
    primary: {
      light: '#93c5fd',
      DEFAULT: '#3b82f6',
      dark: '#1d4ed8'
    },
    // Colores desde API/BD
    dynamic: {
      get primary() {
        return localStorage.getItem('theme-primary') || '#3b82f6';
      }
    }
  },
  
  typography: {
    fontSizes: {
      sm: 'clamp(0.875rem, 1.5vw, 1rem)',
      md: 'clamp(1rem, 2vw, 1.25rem)',
      lg: 'clamp(1.25rem, 3vw, 1.5rem)'
    },
    // Fuentes desde configuración
    fontFamily: {
      body: `var(--font-family-body, 'Inter', sans-serif)`,
      heading: `var(--font-family-heading, 'Montserrat', sans-serif)`
    }
  },
  
  spacing: {
    // Espaciado responsive
    xs: 'clamp(0.25rem, 0.5vw, 0.5rem)',
    sm: 'clamp(0.5rem, 1vw, 1rem)',
    md: 'clamp(1rem, 2vw, 1.5rem)',
    lg: 'clamp(1.5rem, 3vw, 2rem)'
  }
};

// Función para aplicar configuración dinámica
export function applyDynamicConfig(config) {
  const root = document.documentElement;
  
  Object.entries(config.colors.dynamic).forEach(([key, value]) => {
    root.style.setProperty(`--color-${key}`, value);
  });
  
  Object.entries(config.typography.fontFamily).forEach(([key, value]) => {
    root.style.setProperty(`--font-family-${key}`, value);
  });
}
```

## 7. Checklist de Implementación

### ✅ **Diseño y Estructura**
- [ ] Componentes modulares y reutilizables
- [ ] CSS por sección con scoping adecuado
- [ ] Variables CSS dinámicas (nunca hardcodeadas)
- [ ] Sistema de diseño consistente

### ✅ **Accesibilidad**
- [ ] Contraste mínimo 4.5:1
- [ ] Navegación con teclado completa
- [ ] ARIA labels y roles apropiados
- [ ] Screen reader compatible
- [ ] Focus visible en todos los elementos interactivos

### ✅ **Performance**
- [ ] CSS crítico inline (<14KB)
- [ ] Lazy loading de imágenes y componentes
- [ ] Code splitting con Vite
- [ ] Preload de recursos críticos
- [ ] Optimización de imágenes (WebP, AVIF)

### ✅ **SEO y Metadatos**
- [ ] Meta tags dinámicos por página
- [ ] Structured data (JSON-LD)
- [ ] Open Graph tags
- [ ] Twitter Cards
- [ ] Sitemap XML dinámico

### ✅ **Responsive Design**
- [ ] Mobile-first approach
- [ ] Breakpoints basados en contenido
- [ ] Imágenes responsive (srcset)
- [ ] Typography fluid

## 8. Métricas de Calidad

### 8.1. PageSpeed Insights Targets
- **Performance**: >95
- **Accessibility**: 100
- **Best Practices**: 100
- **SEO**: 100

### 8.2. Core Web Vitals
- **LCP (Largest Contentful Paint)**: <2.5s
- **FID (First Input Delay)**: <100ms
- **CLS (Cumulative Layout Shift)**: <0.1

### 8.3. Herramientas de Monitoreo
```javascript
// scripts/performance-monitor.js
export class PerformanceMonitor {
  static logMetrics() {
    // Registrar Core Web Vitals
    if ('webVitals' in window) {
      webVitals.getCLS(console.log);
      webVitals.getFID(console.log);
      webVitals.getLCP(console.log);
    }
    
    // Monitorear recursos
    const resources = performance.getEntriesByType('resource');
    const slowResources = resources.filter(r => r.duration > 1000);
    
    if (slowResources.length > 0) {
      console.warn('Recursos lentos detectados:', slowResources);
    }
  }
}
```

---

**Última actualización**: 30 de marzo de 2026  
**Responsable**: Equipo de Desarrollo  
**Estado**: En implementación
