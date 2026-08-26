# FORTIFY — Guía de Instalación y Compatibilidad con Octane

> **Razón de uso:** Laravel Breeze es incompatible con FrankenPHP/Octane. Fortify es la única solución aprobada para auth en este stack.
> Para arquitectura de auth (Sanctum, Social Login, Filament), ver [auth-system.md](auth-system.md).

---

## 1. Migración Breeze → Fortify

```bash
composer remove laravel/breeze && composer require laravel/fortify
php artisan vendor:publish --tag=fortify-config
php artisan vendor:publish --tag=fortify-migrations
php artisan migrate
echo "SESSION_DRIVER=redis" >> .env
echo "FORTIFY_ENABLED=true" >> .env
php artisan optimize:clear && php artisan octane:reload
```

---

## 2. Configuración Obligatoria (`config/fortify.php`)

```php
'views'    => false,   // Fortify NO sirve vistas propias; las maneja Livewire/Blade
'guard'    => 'web',
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication(['confirm' => true]),
],
```

---

## 3. Rutas Multi-idioma (Integración con i18n)

Desactivar rutas automáticas y registrarlas manualmente dentro del grupo de localización:

```php
// routes/web.php
Fortify::ignoreRoutes();

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'web'],
], function () {
    Route::get('/login',    \App\Livewire\Auth\LoginComponent::class)->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store'])
        ->middleware(['guest', 'throttle:login']);
    // Mismo patrón para: forgot-password, reset-password, verify-email, 2FA
});
```

**Regla crítica:** Nunca usar `redirect()->route('nombre')` desde Fortify callbacks. Usar siempre:
```php
LaravelLocalization::localizeUrl(route('dashboard'))
```

---

## 4. Compatibilidad con Octane

| Problema | Causa | Solución |
|---|---|---|
| Sesiones rotas entre requests | Driver `file`/`database` no thread-safe | `SESSION_DRIVER=redis` + `driver=redis` en `config/session.php` |
| 2FA no persiste | Config no cargada en worker | `'two_factor' => ['enabled' => true]` en `config/fortify.php` |
| Rate limiting bloquea workers | Límite muy bajo en alta concurrencia | `limiters.login = '10,5'` en `config/fortify.php` |
| `RouteNotFoundException` en colas | Worker usa locale fallback | Sobrescribir `verificationUrl()` en notificaciones para inyectar `$locale` |

---

## 5. Testing en Octane

```bash
# Iniciar servidor de prueba
docker compose exec app php artisan octane:start --server=frankenphp --port=8000

# Probar login
curl -X POST http://localhost/es/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=admin@test.com&password=password&_token=TOKEN"
```

---

## 6. Filament Panel Auth (referencia rápida)

```php
// AppPanelProvider.php
$panel->login()->registration()->passwordReset()->emailVerification()->profile();
```
