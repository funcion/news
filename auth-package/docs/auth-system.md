# AUTH — Arquitectura del Sistema de Autenticación

> Stack: **Fortify** (backend/seguridad) + **Livewire + MaryUI** (UI) + **Sanctum** (API móvil).
> Instalación, migración Breeze→Fortify y compatibilidad Octane: ver [FORTIFY.md](FORTIFY.md).

---

## 1. Tres Pilares de Auth

| Pilar | Responsabilidad |
|---|---|
| **Fortify** | Rate limiting, validación, 2FA, gestión de sesión. Nunca `Auth::attempt()` manual. |
| **Livewire + MaryUI** | Renderizado de formularios, reactividad visual, feedback de errores. |
| **Sanctum** | Personal Access Tokens (PATs) para apps móviles. |

---

## 2. Rutas y Multi-idioma (i18n)

Desactivar rutas automáticas de Fortify y registrarlas dentro del grupo de localización:

```php
// routes/web.php
Fortify::ignoreRoutes();

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'web'],
], function () {
    Route::get('/login',    \App\Livewire\Auth\LoginComponent::class)->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:login']);
    Route::get('/register', fn() => view('auth.register'))->name('register');
    // Mismo patrón para: forgot-password, reset-password, verify-email, 2FA
});
```

### Reglas Críticas i18n
1. **Redirecciones:** Usar `LaravelLocalization::localizeUrl(route('nombre'))`. Nunca `redirect()->route()` desde callbacks de Fortify.
2. **URLs Firmadas en Colas:** Las notificaciones (`VerifyEmail`, `ResetPassword`) con `ShouldQueue` **deben** sobrescribir `verificationUrl($notifiable)` para forzar el locale del usuario. Sin esto, el worker de Horizon lanzará `RouteNotFoundException`.

---

## 3. Frontend Seguro (Blade + MaryUI)

Formularios de autenticación usan POST tradicional (no `wire:submit` de Livewire):

```blade
<x-card title="{{ __('Login') }}">
    @include('partials.alerts')
    <form action="{{ LaravelLocalization::localizeUrl(request()->url()) }}" method="POST">
        @csrf
        <x-input name="email" label="Email" type="email" required />
        <x-input name="password" label="Password" type="password" required />
        <x-button type="submit" label="Ingresar" />
    </form>
</x-card>
```

*(Centralizar `session('status')` y `$errors` en `partials/alerts.blade.php` usando `x-alert` de MaryUI).*

---

## 4. API Móvil (Sanctum)

- Único lugar donde se permite validación manual (`Hash::check`) para generar PATs.
- Evaluar siempre 2FA antes de retornar el token definitivo.

```php
// routes/api.php
Route::post('/mobile/login',  [MobileAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/mobile/logout', [MobileAuthController::class, 'logout']);
```

---

## 5. Social Login (Google — Mock Local)

Mock automático en entorno local si `GOOGLE_CLIENT_ID` está vacío (configurar en `AppServiceProvider`).

- **DB:** `google_id` (unique, nullable), `avatar_url` (text, nullable), `password` (nullable).
- Al autenticarse con red social, marcar email como verificado automáticamente.
- Patrón de integración: ver `UserData::fromSocialite()` en [PATTERNS.md](../01-architecture/PATTERNS.md).

---

## 6. Filament Admin Auth

- **Editar perfil:** Implementar `afterSave()` en `EditProfile` para evitar cierre de sesión al editar el propio usuario.
- **Auto-eliminación:** Bloquear que el usuario logueado se elimine a sí mismo (en `UserResource` y queries globales).
- **Panel Provider:** `$panel->login()->registration()->passwordReset()->emailVerification()->profile();`

