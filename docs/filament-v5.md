# 🏗️ Filament v5 — Referencia Rápida para IA

> **v5.x (Latest)** · Filament v5 = v4 API + Livewire v4 + Schemas (nueva capa unificada) + Container Queries + JS expressions + Column Manager rediseñado + Clusters

## 📋 Contexto Esencial

| Aspecto | Realidad |
|---------|----------|
| v5 NO es "solo v4 + Livewire v4" | Tiene cambios API significativos (Schemas, Container Queries, JS expressions, Column Manager, Clusters) |
| Upgrade | `composer require filament/upgrade:"^5.0" -W --dev` → `vendor/bin/filament-v5` → `composer update` |
| Clusters | Nueva forma de agrupar Resources y Pages en navegación (`php artisan make:filament-cluster`) |
| v5.x fixes clave | Closures→methods, reset tabs en create another, re-authorization en hydration, grow en inline schemas, cache cleanup en Select |

## 🧠 Instrucciones para Agentes IA

1. **Verifica features dudosas** en https://filamentphp.com/docs/5.x — si no encuentras el método, no existe
2. **Seguridad**: `afterStateUpdatedJs()`/`actionJs()`/`hiddenJs()` son solo para UI — nunca para lógica de negocio o validación. Combina `hiddenJs()` con `hidden()` server-side si hay datos sensibles
3. **Rendimiento por defecto**: `live(onBlur: true)` para textos largos, `live(debounce: 300)` para búsquedas, `partiallyRenderComponentsAfterStateUpdated()` cuando 1-2 campos dependen del cambio
4. **Fallbacks obligatorios**: Container queries → incluir `!@md`, JS expressions → probar que la UI funciona sin JS
5. **Testing mental**: ¿Funciona sin JS? ¿Es seguro si manipulan el frontend? ¿Escala a 10k registros?

## 🚨 APIs Clave que NO existían en v4 (las IA alucinan fácilmente)

> 🔗 Docs: https://filamentphp.com/docs/5.x/forms/overview · https://filamentphp.com/docs/5.x/schemas/layouts

### Forms — Schema reemplaza a Form
```php
use Filament\Schemas\Schema;
// ANTES: public static function form(Form $form): Form
public static function form(Schema $schema): Schema
{
    return $schema->components([...]); // →components() no →schema()
}
```

### Reactividad — live() con blur/debounce
```php
TextInput::make('qty')->numeric()->live(onBlur: true)
    ->afterStateUpdated(fn (Get $get, Set $set, ?string $s) => $set('total', (float)$s * (float)$get('price')));
TextInput::make('user')->live(debounce: 500);
```

### afterStateUpdatedJs() — JS cliente, 0 network requests
```php
TextInput::make('name')->afterStateUpdatedJs(<<<'JS'
    $set('email', ($state ?? '').replaceAll(' ', '.').toLowerCase() + '@example.com')
JS);
```

### hiddenJs() / visibleJs() — ocultar sin live()
```php
Toggle::make('is_admin')->hiddenJs(<<<'JS' $get('role') !== 'staff' JS);
// También en Infolists: IconEntry::make('is_admin')->boolean()->hiddenJs(...)
```

### Partial Rendering — evitar re-render completo
```php
TextInput::make('name')->live()->partiallyRenderComponentsAfterStateUpdated(['email']);
TextInput::make('name')->live()->partiallyRenderAfterStateUpdated();
TextInput::make('name')->live()->skipRenderAfterStateUpdated();
```

### Type-safe $get()
```php
$get->string('email'); $get->integer('age'); $get->float('price');
$get->boolean('is_admin'); $get->array('tags'); $get->date('published_at');
$get->enum('status', StatusEnum::class); $get->filled('email'); $get->blank('email');
$get->string('email', isNullable: true);
```

### FusedGroup — fusionar campos visualmente
```php
use Filament\Schemas\Components\FusedGroup;
FusedGroup::make([TextInput::make('city'), Select::make('country')])->label('Location')->columns(2);
```

### JsContent — labels con JS
```php
use Filament\Schemas\JsContent;
TextInput::make('greeting')->label(JsContent::make(<<<'JS' ($get('name') === 'John') ? 'Hello!' : 'Stranger!' JS));
```

### 10 Slots de contenido en campos/entries
```php
// aboveLabel, beforeLabel, afterLabel, belowLabel, aboveContent, beforeContent, afterContent, belowContent, aboveErrorMessage, belowErrorMessage
TextInput::make('name')->aboveLabel([Icon::make(Heroicon::Star), 'text'])->beforeContent(Icon::make(Heroicon::Star));
// Alineación: Schema::start([...]), Schema::end([...]), Schema::between([...])
```

### Inline Labels — 3 niveles
```php
TextInput::make('name')->inlineLabel();                          // por campo
Section::make('Details')->inlineLabel()->schema([...]);           // por layout (hereda)
$schema->inlineLabel()->components([...]);                        // todo el schema
```

### Infolists — también migraron a Schema (¡no usar Form!)
```php
use Filament\Schemas\Schema;
// ANTES: public static function infolist(Infolist $infolist): Infolist
public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        TextEntry::make('name'),
        IconEntry::make('is_admin')->boolean(),
    ]);
}
// ⚠️ Las entries usan los mismos slots que Forms: aboveLabel, beforeContent, hiddenJs(), inlineLabel(), etc.
```

### Enums nativos — badge() + HasColor
```php
// El Enum puede definir color/label, evitando match manual
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel {
    case Draft = 'draft';
    case Published = 'published';
    public function getColor(): string | array | null {
        return match ($this) { self::Draft => 'gray', self::Published => 'success' };
    }
    public function getLabel(): string { return $this->name; }
}
// En la tabla:
TextColumn::make('status')->badge()->enum(Status::class); // color y label del Enum
```

### Clusters — agrupar recursos en navegación
```php
// php artisan make:filament-cluster Customer
class CustomerCluster extends Cluster {}
// En cada Resource del cluster:
protected static ?string $cluster = CustomerCluster::class;
```

### Panel Provider — configuraciones globales v5
```php
// En app/Providers/Filament/AdminPanelProvider.php
$panel->spa()                                    // SPA mode sin recargas
    ->unsavedChangesAlerts()                     // alertar cambios sin guardar
    ->font('Inter')                              // fuente personalizada
    ->viteTheme('resources/css/filament/admin.css'); // tema con Vite
```

### disabledOn() / visibleOn() / hiddenOn() / saved()
```php
Toggle::make('is_admin')->disabledOn('edit')->hiddenOn(['edit','view'])->visibleOn('create');
TextInput::make('pwd')->password()->saved(false); // no se guarda
TextInput::make('pwd')->saved(fn (?string $s): bool => filled($s)); // condicional
```

### Relaciones — condition, relatedModel (MorphTo), saveRelationshipsWhenHidden
```php
Group::make()->relationship('customer', condition: fn (?array $s): bool => filled($s['name']));
Group::make()->relationship('customer', relatedModel: Organization::class);
Group::make()->relationship('metadata')->saveRelationshipsWhenHidden()->hidden();
```

### configureUsing() — defaults globales
```php
// En ServiceProvider::boot()
Checkbox::configureUsing(fn (Checkbox $c): void => $c->inline(false));
TextColumn::configureUsing(fn (TextColumn $c): void => $c->toggleable());
TextEntry::configureUsing(fn (TextEntry $e): void => $e->words(10));
```

### extraInputAttributes / extraFieldWrapperAttributes / extraEntryWrapperAttributes
```php
TextInput::make('x')->extraInputAttributes(['width'=>200])->extraFieldWrapperAttributes(['class'=>'x']);
TextEntry::make('x')->extraEntryWrapperAttributes(['class'=>'x']);
TextColumn::make('x')->extraCellAttributes(['class'=>'x'])->extraHeaderAttributes(['class'=>'x']);
```

---

### Tables — ColumnGroup
> 🔗 Docs: https://filamentphp.com/docs/5.x/tables/columns/overview
```php
use Filament\Tables\Columns\ColumnGroup;
ColumnGroup::make('Visibility', [TextColumn::make('status'), IconColumn::make('is_featured')])->alignCenter()->wrapHeader();
```

### Tables — Column Manager API completa
```php
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\ColumnManagerResetActionPosition;
$table->reorderableColumns()
    ->deferColumnManager(false)                                    // live en vez de "Apply"
    ->columnManagerLayout(ColumnManagerLayout::Modal)              // modal vs dropdown
    ->columnManagerColumns(2)
    ->columnManagerTriggerAction(fn (Action $a) => $a->button()->label('Manager'))
    ->columnManagerResetActionPosition(ColumnManagerResetActionPosition::Footer)
    ->persistColumnsInSession(false);
TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true);
```

### Tables — Sorting avanzado
```php
TextColumn::make('full_name')->sortable(['first_name','last_name']);
TextColumn::make('full_name')->sortable(query: fn(Builder $q, string $d) => $q->orderBy('last_name',$d)->orderBy('first_name',$d));
$table->defaultSort('stock', direction:'desc')->defaultSortOptionLabel('Date')->persistSortInSession()->defaultKeySort(false);
```

### Tables — Búsqueda
```php
TextColumn::make('name')->searchable(isIndividual: true, isGlobal: false);
$table->searchable(['id', fn(Builder $q, string $s) => $q->whereYear('published_at',$s)])
    ->searchPlaceholder('Search...')->searchDebounce('750ms')->searchOnBlur()
    ->persistSearchInSession()->persistColumnSearchesInSession()->splitSearchTerms(false);
```

### Tables — Agregaciones en relaciones
```php
TextColumn::make('users_count')->counts('users');
TextColumn::make('users_count')->counts(['users' => fn(Builder $q) => $q->where('is_active', true)]);
TextColumn::make('users_exists')->exists('users');
TextColumn::make('users_avg_age')->avg('users','age');
TextColumn::make('users_max_age')->max('users','age');
TextColumn::make('users_min_age')->min('users','age');
TextColumn::make('users_total')->sum('users','age');
```

### Tables — Utilidades de columna
```php
TextColumn::make('title')->placeholder('Untitled')->grow()->width('1%')
    ->tooltip('x')->headerTooltip('SKU')->wrapHeader()
    ->verticallyAlignStart()->alignEnd()->disabledClick();
```

---

### Actions — actionJs() (JS cliente, sin network)
> 🔗 Docs: https://filamentphp.com/docs/5.x/actions/overview
```php
Action::make('generateSlug')->actionJs(<<<'JS' $set('slug', $get('title').toLowerCase().replaceAll(' ', '-')) JS);
// ⚠️ actionJs() NO puede abrir modales ni ejecutar PHP
```

### Actions — Rate Limiting
```php
Action::make('delete')->rateLimit(5)
    ->rateLimitedNotificationTitle('Slow down!')
    ->rateLimitedNotification(fn (TooManyRequestsException $e) => Notification::make()->warning()->title('Slow!')->body("{$e->secondsUntilAvailable}s"));
```

### Actions — Authorization con feedback visual
```php
Action::make('edit')->authorize('update')
    ->authorizationTooltip()          // tooltip con mensaje de policy, botón disabled
    ->authorizationNotification()     // notificación en vez de ocultar
    ->authorizationMessage('Fallback'); // cuando policy retorna false sin mensaje
```

### Actions — Trigger styles
```php
Action::make('filter')->iconButton()->icon('heroicon-m-funnel')->badge(5)->badgeColor('success')
    ->labeledFrom('md')->outlined()->keyBindings(['command+s','ctrl+s']);
```

### Actions — Como layout component
```php
use Filament\Schemas\Components\Actions;
Actions::make([Action::make('star'), Action::make('reset')->color('danger')])
    ->fullWidth()->alignment(Alignment::Center)->verticalAlignment(VerticalAlignment::End);
```

### Actions — Utility injection en schemas
```php
Action::make('gen')->action(fn (Get $schemaGet, Set $schemaSet) => $schemaSet('slug', str($schemaGet('title'))->slug()));
// También inyecta: $schema, $schemaComponent, $schemaComponentState, $schemaState, $schemaOperation
```

---

### Layouts — Container Queries (breakpoints @md/@xl)
> 🔗 Docs: https://filamentphp.com/docs/5.x/schemas/layouts
```php
use Filament\Schemas\Components\Grid;
Grid::make()->gridContainer()->columns(['@md'=>3, '@xl'=>4])->schema([
    TextInput::make('name')->columnSpan(['@md'=>2,'@xl'=>3])->columnOrder(['default'=>2,'@xl'=>1]),
    TextInput::make('email')->columnSpan(['default'=>1,'@xl'=>1])->columnOrder(['default'=>1,'@xl'=>2]),
]);
// Fallback navegadores antiguos: '!@md'=>2, '!@xl'=>3
```

### Layouts — Flex component
```php
use Filament\Schemas\Components\Flex;
Flex::make([Section::make([...]), Section::make([...])->grow(false)])->from('md');
```

### Layouts — Fieldset sin borde, dense, gap
```php
Fieldset::make('Label')->contained(false)->schema([...]);
Fieldset::make('Dense')->dense()->schema([...]);     // 50% menos spacing
Fieldset::make('No gap')->gap(false)->schema([...]); // sin gap
```

### Layouts — columnOrder / columnStart
```php
Grid::make()->columns(3)->schema([
    TextInput::make('a')->columnOrder(3),  // aparece último
    TextInput::make('b')->columnOrder(1),  // aparece primero
]);
TextInput::make('name')->columnStart(['sm'=>2, 'xl'=>3]);
```

### Layouts — Responsive Grid completo
```php
Section::make()->columns(['sm'=>3,'xl'=>6,'2xl'=>8])->schema([
    TextInput::make('name')->columnSpan(['default'=>1,'sm'=>2,'xl'=>3,'2xl'=>4])->columnOrder(['default'=>2,'xl'=>1]),
    TextInput::make('email')->columnSpan(['default'=>1,'xl'=>2])->columnOrder(['default'=>1,'xl'=>2]),
]);
```

---

### Notifications — Acciones, duración, persistencia
> 🔗 Docs: https://filamentphp.com/docs/5.x/notifications/overview
```php
Notification::make()->title('Saved')->success()->body('OK')->actions([
    Action::make('view')->button()->url(route('posts.show',$post), shouldOpenInNewTab: true),
    Action::make('undo')->color('gray')->dispatch('undoEditingPost',[$post->id])->close(),
])->send();
// Duración: ->duration(5000) | ->seconds(5) | ->persistent()
// Color/icono: ->color('success')->icon('heroicon-o-doc')->iconColor('success')
```

### Notifications — JavaScript API
```javascript
new FilamentNotification().title('Saved').success().body('OK')
    .actions([new FilamentNotificationAction('view').button().url('/view').openUrlInNewTab()]).send();
// Cerrar: $dispatch('close-notification', { id: notificationId })
```

### Notifications — Posicionamiento global
```php
use Filament\Notifications\Livewire\Notifications;
Notifications::alignment(Alignment::Start);
Notifications::verticalAlignment(VerticalAlignment::End);
```

---

### Widgets — Dashboard filters (HasFiltersForm)
> 🔗 Docs: https://filamentphp.com/docs/5.x/widgets/overview
```php
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
class Dashboard extends BaseDashboard {
    use HasFiltersForm;
    public function filtersForm(Schema $schema): Schema {
        return $schema->components([Section::make()->schema([DatePicker::make('startDate')])->columns(3)]);
    }
}
// En el widget:
use Filament\Widgets\Concerns\InteractsWithPageFilters;
class BlogStats extends StatsOverviewWidget {
    use InteractsWithPageFilters;
    public function getStats(): array {
        $start = $this->pageFilters['startDate'] ?? null;
    }
}
```

### Widgets — Filtros con Action Modal (HasFiltersAction)
```php
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Actions\FilterAction;
class Dashboard extends BaseDashboard {
    use HasFiltersAction;
    protected function getHeaderActions(): array {
        return [FilterAction::make()->schema([DatePicker::make('startDate')])];
    }
}
// Persistencia: protected bool $persistsFiltersInSession = false;
```

### Widgets — Múltiples dashboards, grid responsive
```php
protected static string $routePath = 'finance';
protected static ?string $title = 'Finance dashboard';
// Grid responsive:
public function getColumns(): int|array { return ['md'=>4,'xl'=>5]; }
// Widget span responsive:
protected int|string|array $columnSpan = ['md'=>2,'xl'=>3];
// Ocultar condicionalmente:
public static function canView(): bool { return auth()->user()->isAdmin(); }
```

---

### Resources — CLI flags
> 🔗 Docs: https://filamentphp.com/docs/5.x/resources/overview
```bash
php artisan make:filament-resource Customer --simple --generate --soft-deletes --view --model --migration --factory
php artisan make:filament-resource Customer --model-namespace=Custom\\Path\\Models
php artisan make:filament-widget LatestOrders --table
```

### Resources — Estructura v5 (Schemas/Tables separados)
```
Customers/
├── CustomerResource.php        # form(Schema) + table(Table) delegan a Schemas/ y Tables/
├── Pages/{Create,Edit,List}Customer.php
├── Schemas/CustomerForm.php    # configure(Schema)
└── Tables/CustomersTable.php   # configure(Table)
```

### Resources — Sub-navigation
```php
use Filament\Pages\Enums\SubNavigationPosition;
protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End; // Start|Top(tabs)
public static function getRecordSubNavigation(Page $page): array {
    return $page->generateNavigationItems([ViewCustomer::class, EditCustomer::class]);
}
```

### Resources — Ocultar por operación + mejoras
```php
use Filament\Support\Enums\Operation;
TextInput::make('password')->hiddenOn(Operation::Edit)->visibleOn(Operation::Create);
// Propiedades: $hasTitleCaseModelLabel=false, $shouldSkipAuthorization=true, $slug='pending-orders'
// Navegación: $navigationParentItem='Products', $navigationGroup='Shop'
// URLs: CustomerResource::getUrl(panel:'marketing')
// URLs a modales: CustomerResource::getUrl(parameters:['tableAction'=>EditAction::getDefaultName(),'tableActionRecord'=>$customer])
```

---

## 🚫 Anti-Patrones Comunes (lo que las IAs suelen generar mal)

| Anti-patrón | ❌ Mal | ✅ Bien |
|-------------|--------|---------|
| `live()` sin control en inputs de texto | `TextInput::make('s')->live()` | `->live(debounce:300)` o `->live(onBlur:true)` |
| `actionJs()` para lógica de negocio | `Action::make('save')->actionJs(<<<'JS' ... JS)` | `Action::make('save')->action(fn() => ...)` |
| Container queries sin fallback | `->columns(['@md'=>3])` | `->columns(['@md'=>3, '!@md'=>2])` |
| `hiddenJs()` sin base server-side | `->hiddenJs(...)` solo | `->hidden(fn() => !admin)->hiddenJs(...)` combinado |
| `afterStateUpdatedJs()` para validación | JS que valida contra BD | Usar `afterStateUpdated()` con PHP + `$set()` |
| No usar `authorize()` en Actions | Solo `->visible(fn() => can('update', $r))` | `->authorize('update')->authorizationTooltip()` |
| Ignorar N+1 en columnas | `TextColumn::make('author.name')` sin `->preload()` | `->preload()` + `getEloquentQuery()->with('author')` |
| Closure cuando basta un valor estático | `->label(fn() => __('Name'))` | `->label(__('Name'))` (v5 prefiere valores planos o `JsContent`) |
| Modales con relaciones pesadas en schema | Cargar todo en `->schema([...])` | Usar `->modalContentFooter(view(...))` o `->lazy()` si es complejo |
