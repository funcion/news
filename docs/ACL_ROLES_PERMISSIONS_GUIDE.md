# 🛡️ Guía Maestra de Control de Acceso (ACL): Roles, Permisos y Seguridad en Glodaxia

Esta guía documenta la arquitectura integral de **Control de Acceso (ACL)** implementada en Glodaxia, combinando el estándar de la industria **`spatie/laravel-permission`** con la suite visual **`bezhansalleh/filament-shield`** y las **Policies Nativas de Laravel**.

---

## 🏛️ 1. Arquitectura de Doble Nivel (Staff vs Lectores)

El sistema opera bajo una estricta separación entre el equipo editorial interno y la comunidad de lectores públicos:

```mermaid
graph TD
    subgraph BACKEND["🏢 1. Panel de Administración (/admin)"]
        SA["👑 Super Admin (Tú)"]
        ED["👔 Editor Jefe"]
        RED["✍️ Redactor / Periodista"]
        MOD["🛡️ Moderador de Comunidad"]
    end

    subgraph FRONTEND["🌐 2. Portal Web Público (Glodaxia.com)"]
        LEC["📖 Lector Registrado"]
        VIS["👤 Visitante Anónimo (Solo Lectura)"]
    end

    User["Modelo User Único"] -->|canAccessPanel() = true| BACKEND
    User -->|canAccessPanel() = false| FRONTEND
```

---

## 👥 2. Matriz de Roles y Alcance de Permisos

| Rol | Guard | Panel Admin (`/admin`) | Alcance Editorial y Capacidades |
| :--- | :---: | :---: | :--- |
| **`super_admin`** | `web` | ✅ Total | Acceso absoluto a todas las configuraciones, API keys, logs, bases de datos y roles vía `Gate::before`. |
| **`editor_jefe`** | `web` | ✅ Total | Puede crear, editar, revisar, programar y publicar noticias de cualquier redactor. Gestiona Fuentes RSS, Categorías y Tags. |
| **`redactor`** | `web` | ✅ Restringido | Puede crear y editar sus propios artículos en estado *Borrador* y enviarlos a revisión. Visualiza Noticias Crudas. |
| **`moderador`** | `web` | ✅ Restringido | Acceso exclusivo al módulo de **Comentarios** para aprobar, rechazar o marcar spam. |
| **`lector`** | `web` | ❌ Bloqueado | Usuario registrado en la web. Puede comentar (sujeto a moderación), dar likes y gestionar su perfil/foto en Cloudflare R2. |

---

## 🎛️ 3. Módulos de Gestión Visual en Filament

### A. Matriz Visual de Roles y Permisos ([`/admin/shield/roles`](http://localhost:8000/admin/shield/roles))
* Permite crear nuevos roles y editar los existentes con una matriz interactiva de checkboxes organizados por recurso:
  * 📰 **Artículos:** `ViewAny`, `View`, `Create`, `Update`, `Delete`, `DeleteAny`.
  * 🔍 **Noticias Crudas:** `ViewAny`, `View`, `Create`, `Update`, `Delete`.
  * 💬 **Comentarios:** `ViewAny`, `View`, `Update`, `Delete`.
  * 📡 **Fuentes RSS:** `ViewAny`, `View`, `Create`, `Update`, `Delete`.
  * 🏷️ **Categorías y Tags:** `ViewAny`, `View`, `Create`, `Update`, `Delete`.
  * ⚙️ **Páginas & Widgets:** Permisos de acceso a `Dashboard`, `SettingsPage`, etc.

### B. Asignación de Roles a Usuarios ([`/admin/users`](http://localhost:8000/admin/users))
* Al crear o editar cualquier usuario del equipo, dispones de un selector múltiple **"Roles de Acceso"** para asignarle su cargo con un solo clic.
* La tabla de usuarios muestra las etiquetas de roles asociadas en colores diferenciados (*Rojo: Super Admin, Azul: Editor Jefe, Cyan: Redactor, Ámbar: Moderador*).

---

## 💻 4. Uso en Código (Backend & Frontend)

### A. En Vistas Blade del Frontend:
```blade
{{-- Verificar si puede moderar comentarios --}}
@can('Update:Comment')
    <a href="/admin/comments" class="btn">Moderar Comentarios</a>
@endcan

{{-- Verificar si es miembro del Staff para mostrar el acceso al Admin --}}
@if(auth()->user()->hasAnyRole(['super_admin', 'editor_jefe', 'redactor', 'moderador']))
    <a href="/admin">Panel Admin</a>
@endif

{{-- Verificar si es un Lector para habilitar caja de comentarios --}}
@auth
    <form action="/comment" method="POST">
        @csrf
        <textarea name="content"></textarea>
        <button type="submit">Publicar Comentario</button>
    </form>
@else
    <p>Inicia sesión para comentar en este artículo.</p>
@endauth
```

### B. En Controladores y Componentes Livewire:
```php
// Asignar rol de lector automáticamente al registrarse
$user = User::create([...]);
$user->assignRole('lector');

// Verificar permisos antes de ejecutar una acción sensible
if ($user->can('Update:Article')) {
    $article->update($request->validated());
}
```

### C. En Policies de Laravel (`app/Policies/*`):
Filament y Laravel consultan automáticamente las Policies generadas para cada modelo:
* `ArticlePolicy.php`
* `RawArticlePolicy.php`
* `CommentPolicy.php`
* `SourcePolicy.php`
* `CategoryPolicy.php`
* `UserPolicy.php`

---

## ⚙️ 5. Comandos de Mantenimiento y Regeneración

Si en el futuro agregas un nuevo recurso, página o widget a Filament, puedes sincronizar los permisos con:

```bash
# Escanear y regenerar permisos y policies de todos los recursos
php artisan shield:generate --all --panel=admin --option=policies_and_permissions --no-interaction

# Asignar rol Super Admin a un usuario específico por ID
php artisan shield:super-admin --user=1

# Limpiar la memoria caché de permisos en Redis
php artisan permission:cache-reset
```

---

## ⚡ 6. Rendimiento y Caché en Producción (Redis)

* Los permisos de cada usuario se compilan y almacenan en la memoria RAM de **Redis** en la primera solicitud.
* Las validaciones de acceso subsiguientes se ejecutan en **0.1 milisegundos**, garantizando cero impacto en el tiempo de carga del portal.