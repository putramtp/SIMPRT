# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SIPRT** (Sistem Informasi Penugasan dan Pelaporan Teknisi) is a Laravel 10 task management and reporting system for managing sales assignments and technician work reports. It uses a mobile-first UI with role-based access control (RBAC) via Spatie Laravel Permission.

## Common Commands

```bash
# Run migrations
php artisan migrate

# Seed roles & permissions
php artisan db:seed

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear

# Run tests
./vendor/bin/phpunit

# Code formatting (Laravel Pint)
./vendor/bin/pint

# Interactive shell
php artisan tinker
```

## Architecture

### Stack
- **Backend**: Laravel 10, PHP 8.1+, MySQL (`db_siprt`)
- **Auth**: Laravel UI (session-based) + Laravel Sanctum (API tokens) + Spatie Laravel Permission v6 (RBAC)
- **Frontend**: Bootstrap 5 (CDN), jQuery 3.7.1 (CDN), DataTables 1.13.8 (CDN), static `public/css/public.css` and `public/css/public.js`
- **DataTables**: `yajra/laravel-datatables-oracle` v10.11.4 — server-side processing with Ajax on all index tables

### Routing
- Web routes in `routes/web.php`; all feature routes are inside the `auth` middleware group
- Authentication routes auto-registered via `Auth::routes()`
- Each menu is a dedicated Laravel route and Blade view — no JS show/hide toggling

### Menu Structure
| Menu | Route | Controller Method |
|---|---|---|
| Dashboard Sales | `GET /dashboard/sales` | `DashboardController@sales` |
| Dashboard Teknisi | `GET /dashboard/teknisi` | `DashboardController@teknisi` |
| Buat Tugas | `GET /tugas/create` | `TugasController@create` |
| Form Laporan | `GET /laporan/create` | `LaporanController@create` |
| Custom Template | `GET /template` | `TemplateController@index` |
| Laporan Customer | `GET /customers/{customer}/laporan` | `CustomerController@laporan` |

### Migrations
All tables are created and migrated:

| Table | Purpose |
|---|---|
| `users` | Application users |
| `customers` | Customer records (name, phone, email, address) |
| `tasks` | Assignments (title, description, customer_id, assigned_to, created_by, status, due_date) |
| `reports` | Technician work reports (task_id, user_id, description, status, photo) |
| `technicians` | Technician profiles linked to users (user_id, specialization, phone) |
| `roles`, `permissions`, etc. | Spatie RBAC tables |
| `personal_access_tokens` | Sanctum API tokens |
| `failed_jobs` | Queue failure tracking |

### Models
Located in `app/Models/`:

| Model | Key Relationships |
|---|---|
| `User` | `HasRoles`, `HasApiTokens`; assigned tasks via `tasks` table |
| `Customer` | `hasMany(Task)` |
| `Task` | `belongsTo(Customer)`, `assignee` (User), `creator` (User), `hasMany(Report)` |
| `Report` | `belongsTo(Task)`, `teknisi` (User) |
| `Technician` | `belongsTo(User)` |

When adding new models: `php artisan make:model ModelName -mfsc` scaffolds migration, factory, seeder, and controller together.

### Controllers
Located in `app/Http/Controllers/`:

| Controller | Type | Responsibility |
|---|---|---|
| `DashboardController` | Custom | `sales()` and `teknisi()` dashboard views |
| `TugasController` | Resource | Full CRUD for tasks; `index()` returns DataTables JSON on Ajax |
| `LaporanController` | Resource | Full CRUD for reports; file upload; `index()` returns DataTables JSON on Ajax |
| `CustomerController` | Resource | Full CRUD for customers; `index()` and `laporan()` return DataTables JSON on Ajax |
| `UserController` | Resource | User management with role assignment; `index()` returns DataTables JSON on Ajax |
| `TemplateController` | Custom | Template index page |
| `HomeController` | Custom | Default `/home` landing page — JS redirect based on role |
| `Auth/*` | Laravel UI | Do not modify |

#### DataTables Ajax Pattern
All `index()` methods that power a table follow this pattern — do not break it:
```php
public function index(Request $request)
{
    if ($request->ajax()) {
        $query = Model::with([...])->latest();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('computed_col', fn($row) => ...)
            ->addColumn('action', function ($row) {
                // Use auth()->user()->can() here, NOT @can Blade directive
                return '<a href="...">...</a>';
            })
            ->rawColumns(['action'])   // list every column that contains HTML
            ->make(true);
    }
    return view('module.index');      // no data passed — table is populated via Ajax
}
```
- The view sends an XHR automatically (DataTables sets `X-Requested-With: XMLHttpRequest`)
- `addIndexColumn()` provides `DT_RowIndex` for the `#` column
- Permission checks inside `action` column use `auth()->user()->can('permission')`

### Seeders
Located in `database/seeders/`:

- **DatabaseSeeder** — calls `RolePermissionSeeder` then `DummyDataSeeder`
- **RolePermissionSeeder** — seeds 3 roles (`admin`, `sales`, `teknisi`) and 10 permissions
- **DummyDataSeeder** — seeds 1 admin, 2 sales, 5 teknisi users, 10 customers, 20 tasks, and reports for in-progress/completed tasks

Reset and re-seed the entire database:
```bash
php artisan migrate:fresh --seed
```

Re-seed only permissions (non-destructive):
```bash
php artisan db:seed --class=RolePermissionSeeder
```

Fixed admin account created by `DummyDataSeeder`: `admin@siprt.com` / `password`

### Factories
Located in `database/factories/`:

| Factory | States |
|---|---|
| `UserFactory` | `unverified()` |
| `CustomerFactory` | — |
| `TaskFactory` | `pending()`, `inProgress()`, `completed()` |
| `ReportFactory` | `submitted()`, `approved()` |
| `TechnicianFactory` | — |

Use in tests: `Task::factory()->completed()->create()` or `User::factory(5)->create()->each(fn($u) => $u->assignRole('teknisi'))`.

### RBAC (Spatie Laravel Permission)
Fully wired: `User` model uses `HasRoles`, roles and permissions are seeded, routes are guarded.

#### Roles & Permissions
| Permission | admin | sales | teknisi |
|---|:---:|:---:|:---:|
| `view users` | ✓ | | |
| `create users` | ✓ | | |
| `edit users` | ✓ | | |
| `delete users` | ✓ | | |
| `assign roles` | ✓ | | |
| `view customers` | ✓ | ✓ | ✓ |
| `create customers` | ✓ | ✓ | |
| `edit customers` | ✓ | ✓ | |
| `delete customers` | ✓ | | |
| `view customer reports` | ✓ | ✓ | |

Use `@can('permission name')` in Blade and `->middleware('can:permission name')` on routes.

### Frontend Assets
Static files only — no build step:

- `public/css/public.css` — global styles (CSS variables, PWA layout, mobile-first)
- `public/css/public.js` — intentionally minimal stub (just a comment); **all** sidebar/overlay toggle logic lives in the inline IIFE inside `app.blade.php`

These are loaded in `resources/views/layouts/app.blade.php` via `asset()`. Do **not** use `@vite()`, `npm run dev`, or `npm run build`.

`public.js` is loaded with a `filemtime` cache-buster to prevent browser caching of stale versions:
```html
<script src="{{ asset('css/public.js') }}?v={{ filemtime(public_path('css/public.js')) }}"></script>
```

CDN load order in `app.blade.php` (order matters):
1. Bootstrap 5 CSS + Bootstrap Icons + Tabler Icons + DataTables CSS
2. `public.css`
3. `@yield('css')` — page-specific styles
4. jQuery 3.7.1
5. Bootstrap 5 JS bundle
6. Inline IIFE — sidebar toggle (`#menuToggle` ↔ `#sidebar` + `#sidebarOverlay`), password visibility
7. DataTables core + Bootstrap 5 theme + Responsive extension
8. `public.js` (stub, cache-busted)
9. `@yield('js')` — page-specific scripts

#### PWA Layout
- **Mobile (< 768px)**: full-width, no sidebar, bottom navigation bar
- **Desktop (≥ 768px)**: fixed sidebar 220px left, content fills remaining space via `margin-left: var(--sidebar-w)`
- Sidebar overlay uses `opacity: 0 / pointer-events: none` (not `display: none`) to avoid `backdrop-filter` rendering bugs in Safari. JS adds/removes `.show` class which sets `opacity: 1 / pointer-events: auto`
- Do **not** add a second click listener on `#menuToggle` anywhere — the IIFE in `app.blade.php` is the single source of truth

#### DataTables View Pattern
Index views have an empty `<table id="...">` with `<thead>` only. All data loads via Ajax. Initialize in `@section('js')`:
```js
$(function () {
    $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("module.index") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
            { data: 'column',      name: 'db_column' },
            { data: 'action',      name: 'action', orderable: false, searchable: false },
        ],
        language: { /* Bahasa Indonesia labels */ },
    });
});
```
- Relationship columns (e.g. `customer.name`) set `searchable: false` — server-side search on joins is not wired
- `defaultContent: '-'` handles nullable columns without breaking the table
