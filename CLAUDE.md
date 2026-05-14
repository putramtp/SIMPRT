# CLAUDE.md

Guidance for Claude Code when working in this repository.

---

## Project

**SIPRT** — Laravel 10 task assignment & technician reporting system.
**Stack:** Laravel 10, PHP 8.1+, MySQL (`db_siprt`), Bootstrap 5 CDN, jQuery 3.7.1 CDN, Yajra DataTables, Spatie Permission v6, barryvdh/laravel-dompdf, Laravel Sanctum.
**All 5 development phases complete. Next: deployment.**

### Actors

| Role | Can do |
|---|---|
| `admin` / `sales` | Create tasks, manage customers/users, build templates, view all reports |
| `teknisi` | View assigned tasks, submit reports |
| Customer | Read-only report access via signed URL (no auth) |

---

## Commands

```bash
php artisan migrate
php artisan db:seed                          # RolePermission + DummyData
php artisan migrate:fresh --seed
php artisan db:seed --class=RolePermissionSeeder
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear
php artisan test
./vendor/bin/pint
```

Admin account: `admin@siprt.com` / `password`

---

## Architecture

### Routes
All web feature routes live inside `middleware('auth')`. Key auth rules:
- `role:admin|sales` — tugas create/edit/delete, template store/destroy
- `role:teknisi` — laporan create/store
- `can:view users` — users resource
- `can:view customers` — customers resource + laporan
- `GET /c/{customer}/laporan` — public signed URL (no auth)
- `GET /api/*` — Sanctum token auth (see `routes/api.php`)

### RBAC
Spatie v6 — middleware aliases in `Kernel.php` use `\Spatie\Permission\Middleware\` (no trailing 's').

Permissions: `view/create/edit/delete users`, `assign roles`, `view/create/edit/delete customers`, `view customer reports`.

### DataTables Ajax pattern — do not break

```php
public function index(Request $request)
{
    if ($request->ajax()) {
        return DataTables::of(Model::with([...])->latest())
            ->addIndexColumn()
            ->addColumn('action', fn($row) => '...')   // use auth()->user()->can(), NOT @can
            ->rawColumns(['action'])
            ->make(true);
    }
    return view('module.index');  // no data — Ajax populates
}
```

### CDN load order in `app.blade.php` (order matters)
Bootstrap CSS → Icons → DataTables CSS → `public.css` → `@yield('css')` → jQuery → Bootstrap JS → **inline IIFE** (sidebar toggle) → DataTables JS → `public.js` → `@yield('js')`

---

## Design System

Tokens in `public/css/public.css` — **never hardcode hex**.

```
--blue / --blue-dark / --blue-light
--green / --green-light   --yellow / --yellow-light   --orange   --red
--bg / --card-bg / --border / --border-light / --text / --text-secondary
--sidebar-w: 220px   --topbar-h: 56px
--radius-sm / --radius-md / --radius-lg / --radius-xl
```

Breakpoints: mobile `< 640px` · tablet `640–1023px` · desktop `≥ 1024px` (mobile-first).

---

## Mockup Reference

`/mockup/index.html` — all 6 screens. Do not delete.

| # | Screen | Blade view |
|---|---|---|
| H1 | Dashboard Sales | `dashboard/sales.blade.php` |
| H2 | Form Buat Tugas | `tugas/create.blade.php` |
| H3 | Dashboard Teknisi | `dashboard/teknisi.blade.php` |
| H4 | Form Laporan | `laporan/create.blade.php` |
| H5 | Template Builder | `template/index.blade.php` |
| H6 | Laporan Customer | `laporan/customer.blade.php` |

---

## Hard Rules

1. **No `@vite()`, npm, or build step** — CDN + `public/css/` only
2. **No second `#menuToggle` listener** — the IIFE in `app.blade.php` is the only one
3. **DataTables Ajax pattern** — exactly as above; never pass data to the index view
4. **CSS tokens only** — no hardcoded hex anywhere
5. **`auth()->user()->can()`** in controller action columns — not `@can`
6. **CDN load order** — must match the sequence above
7. **Mobile-first CSS** — base = mobile, add `@media (min-width: 640px)` and `@media (min-width: 1024px)`
8. **Spatie middleware** — `\Spatie\Permission\Middleware\RoleMiddleware` (no 's' at end of Middleware)
