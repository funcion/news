# GUIA DE INTEGRACION DE AUTH (FORTIFY + I18N + LIVEWIRE)

> **Instrucciones para IA / Desarrollador:**
> Este paquete contiene la arquitectura completa, limpia y desacoplada del sistema de autenticacion de alta concurrencia (compatible con Laravel Octane/FrankenPHP, Livewire y Multi-idioma).
> Sigue estos pasos en orden estricto para integrarlo en el nuevo proyecto.

---

## 1. Dependencias Requeridas (Composer & NPM)

Ejecuta en el contenedor/entorno del proyecto de destino:

\\\ash
# 1. Core de Autenticacion y DTOs
composer require laravel/fortify spatie/laravel-data mcamara/laravel-localization

# 2. UI y Componentes (Livewire + MaryUI)
composer require livewire/livewire robsontenorio/mary
npm install -D daisyui@latest @tailwindcss/typography

# 3. Opcionales (segun requerimientos del proyecto)
# Roles y Permisos: composer require spatie/laravel-permission bezhansalleh/filament-shield
# Social Login:     composer require laravel/socialite
# Billeteras:       composer require bavix/laravel-wallet
# Auditoria:        composer require spatie/laravel-activitylog
\\\

---

## 2. Mapeo de Archivos a Copiar

Copia cada carpeta/archivo de este paquete a la raiz de tu proyecto Laravel respetando la estructura:

| Origen en este paquete | Destino en el proyecto | Proposito |
|---|---|---|
| app/Core/Actions/BaseAction.php | app/Core/Actions/BaseAction.php | Clase abstracta para Actions |
| app/Core/Data/BaseData.php | app/Core/Data/BaseData.php | Clase abstracta para DTOs de Spatie Data |
| app/Modules/User/Actions/* | app/Modules/User/Actions/ | Logica pura de registro, cambio de clave, perfil y socialite |
| app/Modules/User/Data/UserData.php | app/Modules/User/Data/UserData.php | DTO inmutable de usuario |
| app/Notifications/Auth/* | app/Notifications/Auth/ | Emails en cola (QueuedResetPassword, QueuedVerifyEmail) con soporte i18n |
| app/Providers/FortifyServiceProvider.php | app/Providers/FortifyServiceProvider.php | Vinculacion de vistas y actions con Fortify |
| app/Http/Middleware/ForceLocale.php | app/Http/Middleware/ForceLocale.php | Middleware para forzar el idioma |
| app/Livewire/Profile/EditProfile.php | app/Livewire/Profile/EditProfile.php | Componente Livewire de edicion de perfil y clave |
| app/Models/User.php | app/Models/User.php | Modelo User con contratos MustVerifyEmail, HasLocalePreference y casts |
| config/fortify.php | config/fortify.php | Configuracion de Fortify (views => false, features 2FA, etc.) |
| config/auth.php | config/auth.php | Configuracion de guards y password resets |
| database/migrations/* | database/migrations/ | Migraciones de usuarios, 2FA, campos extendidos e i18n |
| lang/ | lang/ | Traducciones de autenticacion y slugs de rutas (en, es, pt) |
| resources/views/auth/* | resources/views/auth/ | Vistas Blade (Login, Register, 2FA, Forgot/Reset Password, Verify Email) |
| resources/views/mail/auth/* | resources/views/mail/auth/ | Plantillas Markdown de correos de verificacion y reseteo |
| resources/views/livewire/profile/* | resources/views/livewire/profile/ | Vista Blade para editar perfil y seguridad |
| resources/views/partials/alerts.blade.php | resources/views/partials/alerts.blade.php | Feedback de alertas y errores de sesion |
| resources/views/layouts/auth.blade.php | resources/views/layouts/auth.blade.php | Layout principal para pantallas de auth |
| routes/auth.php | routes/auth.php | Rutas de auth localizadas y aisladas |
| docs/* | docs/ | Guias de arquitectura y referencia tecnica |

---

## 3. Registro de ServiceProvider

En Laravel 11/12+, agrega FortifyServiceProvider a bootstrap/providers.php:

\\\php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];
\\\

---

## 4. Configurar Rutas en routes/web.php

En tu archivo routes/web.php, dentro del grupo de rutas de Mcamara LaravelLocalization, incluye las rutas de auth:

\\\php
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    // Rutas de Autenticacion
    require __DIR__ . '/auth.php';

    // Rutas de Perfil
    Route::get(LaravelLocalization::transRoute('routes.profile'), \App\Livewire\Profile\EditProfile::class)
        ->middleware(['auth', 'verified'])
        ->name('profile.edit');
        
    // Tu Dashboard / Home
    Route::get('/dashboard', fn() => view('dashboard'))
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
});
\\\

---

## 5. Base de Datos y Migraciones

Ejecuta las migraciones:

\\\ash
php artisan migrate
\\\

> **Nota sobre el modelo User:**
> Si el proyecto de destino NO usa bavix/laravel-wallet o spatie/laravel-activitylog, simplemente retira esos dos traits (HasWallets, LogsActivity) del modelo app/Models/User.php.

---

## 6. Reglas Criticas de Rendimiento y Octane
1. **Manejo de Sesiones:** Usa SESSION_DRIVER=redis o database para compatibilidad con workers concurrentes de Octane.
2. **i18n en Colas:** Las notificaciones QueuedVerifyEmail y QueuedResetPassword usan ShouldQueue y resuelven el locale del usuario con \->preferredLocale() para evitar excepciones RouteNotFoundException en Horizon.
3. **Formularios Auth:** Usan POST tradicional garantizando proteccion CSRF y rate limiting nativo de Fortify.

---

## 7. Checklist de Verificacion
- [ ] Acceder a /es/iniciar-sesion o /en/login y verificar renderizado.
- [ ] Registrar un usuario nuevo en /es/registro y verificar el envio del email en cola.
- [ ] Probar flujo de recuperacion de clave en /es/olvide-mi-contrasena.
- [ ] Probar Two-Factor Authentication (2FA) y codigos de recuperacion.
- [ ] Probar edicion de perfil y cambio de clave en /es/perfil.
