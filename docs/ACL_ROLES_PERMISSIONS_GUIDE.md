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
| **`super_admin`** | `web` | ✅ Total | Acceso absoluto a todas las configuraciones, API keys, logs, bases de datos y roles vía `Gate::before`. (Tú) |
| **`admin`** | `web` | ✅ Total | Administrador / Editor Jefe. Gestiona artículos, aprueba noticias, fuentes RSS, categorías y tags. |
| **`redactor`** | `web` | ✅ Restringido | Periodista / Redactor. Puede crear y editar artículos y ver noticias crudas. |
| **`panel_user`** | `web` | ❌ Bloqueado del Admin | **Rol por defecto para todo usuario registrado.** Puede comentar, dar likes y gestionar su perfil/avatar en Cloudflare R2. |

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
# 🐳 Escanear y regenerar permisos y policies de todos los recursos (Vía Docker)
docker compose exec app php artisan shield:generate --all --panel=admin --option=policies_and_permissions --no-interaction

# 🐳 Asignar rol Super Admin a un usuario específico por ID (Vía Docker)
docker compose exec app php artisan shield:super-admin --user=1

# 🐳 Limpiar y resetear la memoria caché de permisos en Redis (Vía Docker)
docker compose exec app php artisan permission:cache-reset

# 🐳 Re-ejecutar seeders de roles y permisos si reinicias la base de datos
docker compose exec app php artisan db:seed
```

---

## ⚡ 6. Rendimiento y Caché en Producción (Redis)

* Los permisos de cada usuario se compilan y almacenan en la memoria RAM de **Redis** en la primera solicitud.
* Las validaciones de acceso subsiguientes se ejecutan en **0.1 milisegundos**, garantizando cero impacto en el tiempo de carga del portal.
---

## 🎯 7. Asignación de Permisos Específicos Directos a Usuarios

Además de los roles (que agrupan paquetes de permisos), el sistema permite otorgar **permisos individuales específicos a cualquier usuario**:

### ¿Cómo funciona en el Panel de Administración?
1. Ve a **Administración ➔ Usuarios** ([`/admin/users`](http://localhost:8000/admin/users)).
2. Edita cualquier usuario o crea uno nuevo.
3. Dispones de dos secciones visuales:
   * **Roles Asignados:** Checkboxes visuales en 4 columnas para marcar los roles del usuario (`super_admin`, `admin`, `redactor`, `panel_user`).
   * **Permisos Específicos Directos (Matriz Visual):** La misma matriz interactiva de Shield con tarjetas por recurso (*Articles, Comments, Sources, etc.*), pestañas de Páginas/Widgets, checkboxes por acción y botón *Select All* por entidad.
2. Edita cualquier usuario o crea uno nuevo.
3. En la sección **"Control de Acceso: Roles y Permisos Directos"**:
   * **Roles Asignados:** Selecciona uno o varios roles generales (*Editor Jefe, Redactor, Moderador, etc.*).
   * **Permisos Específicos Directos (Opcional):** Selecciona permisos individuales adicionales (ej. si un Redactor necesita permisos de `Update:Source` o `ViewAny:Comment` sin cambiarle su rol principal).
4. Guarda los cambios. El usuario tendrá inmediatamente sus permisos combinados (los heredados de sus roles + los directos asignados).

### Verificación en Código:
```php
// Comprobar si el usuario tiene el permiso (ya sea por Rol o Directo)
if ($user->can('Update:Article')) {
    // Permitido
}

// Comprobar si el permiso fue asignado de forma directa e individual
if ($user->hasDirectPermission('Update:Article')) {
    // Es un permiso directo
}
```
---

## ✍️ 8. Regla Estricta de Autoría de Artículos (Exclusión de `panel_user`)

Para proteger la integridad editorial y evitar que usuarios lectores o registrados desde el frontend aparezcan como creadores de noticias:

### 🔒 Reglas de Negocio Implementadas:
1. **Solo Redactores y Staff Editorial:**
   * Únicamente los usuarios con roles **`redactor`**, **`admin`** o **`super_admin`** pueden ser vinculados como autores (`user_id`) de un artículo.
2. **Generador Autónomo de IA (`ProcessArticleWithAIJob`):**
   * Al seleccionar aleatoriamente el autor de la noticia, el pipeline filtra estrictamente con:
     ```php
     $author = User::role(['redactor', 'admin', 'super_admin'])
         ->where('is_active', true)
         ->inRandomOrder()
         ->first();
     ```
3. **Panel Administrativo Filament (`ArticleResource`):**
   * El campo desplegable **"Autor"** (`user_id`) está filtrado a nivel de consulta Eloquent:
     ```php
     Select::make('user_id')
         ->relationship('user', 'name', fn ($query) => $query->role(['redactor', 'admin', 'super_admin'])->where('is_active', true))
         ->searchable()
         ->preload()
         ->required();
     ```
4. **Usuarios Lectores (`panel_user`):**
   * Todo usuario registrado de forma pública recibe el rol `panel_user`. Este rol les otorga acceso a comentar y personalizar su perfil, pero **nunca** aparecerán en los listados de redactores ni podrán ser seleccionados como autores de noticias.
---

## 👑 9. Regla de Oro: Unicidad del SuperAdmin y Reasignación Automática

Para garantizar la máxima seguridad y la integridad referencial de los contenidos:

### 1. Regla de SuperAdmin Único (No pueden existir 2 SuperAdmins):
* En toda la plataforma existe **1 solo y único usuario con el rol `super_admin`** (`admin@glodaxia.com`).
* El Super Administrador principal está **blindado por Observer y Policies**:
  * No puede ser eliminado ni por el panel administrativo ni por comandos de base de datos accidentales.
  * El botón "Eliminar" en Filament está deshabilitado para el Super Administrador.
* Los demás miembros del equipo directivo (como los fundadores y directores de tecnología) cuentan con el rol **`admin`** (Editor Jefe / Administrador).

### 2. Reasignación Automática de Artículos al Eliminar un Redactor (`UserObserver`):
* Si en algún momento se da de baja o se elimina a un redactor del equipo (`UserObserver::deleting`):
  * **Los artículos NO se eliminan ni quedan huérfanos con enlaces rotos.**
  * El sistema transfiere automáticamente la autoría de todos sus artículos creados al **Super Administrador titular (`super_admin`)**.
  * La plataforma registra en los logs de auditoría la cantidad exacta de artículos transferidos.