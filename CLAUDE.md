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
| `teknisi` | View assigned tasks, submit reports; must set signature on first login |
| `customer` | Login → `dashboard.customer`; see only their company's reports; linked via `users.customer_id` → `customers.id` |
| Public | Read-only report access via signed URL (`/c/{customer}/laporan`) — no auth |

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
- `role:admin|sales` — tugas create/edit/delete, template store/destroy, `dashboard.sales`, `dashboard.teknisi.all`
- `role:teknisi` — laporan create/store, `dashboard.teknisi.my`
- `role:customer` — `dashboard.customer`
- `can:view users` — users resource
- `can:view customers` — customers resource + laporan
- `GET /c/{customer}/laporan` — public signed URL (no auth)
- `GET /api/*` — Sanctum token auth (see `routes/api.php`)

**Teknisi dashboards:**
- `/dashboard/teknisi/all` → `DashboardController@teknisiAll` (admin/sales) — all tasks, DataTables Ajax + Chart.js charts
- `/dashboard/teknisi/my`  → `DashboardController@teknisiMy`  (teknisi)      — own tasks, mobile-first card layout

**Route ordering rule:** explicit paths (`tugas/create`, `laporan/create`) must be declared BEFORE wildcard routes (`tugas/{tugas}`, `laporan/{laporan}`) or Laravel will match them as model IDs.

### RBAC
Spatie v6 — middleware aliases in `Kernel.php` use `\Spatie\Permission\Middleware\` (no trailing 's').

Permissions: `view/create/edit/delete users`, `assign roles`, `view/create/edit/delete customers`, `view customer reports`.

Fixed seeded accounts (password: `password`):
- `admin@siprt.com` — role: admin
- `sales@siprt.com` — role: sales
- `teknisi@siprt.com` — role: teknisi
- `customer@siprt.com` — role: customer, linked to PT Maju Jaya Abadi

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

### Template field structure (`templates.fields` JSON column)
Fields are stored as an **array of sections**, each containing an array of fields — NOT a flat field array:

```javascript
// templates.fields — stored as JSON, cast to array in Template model
[
  {
    id: 's1',
    title: 'Section Name',
    fields: [
      { id: 'f1', type: 'text|textarea|number|date|checkbox|select|photo|signature',
        label: '...', placeholder: '...', required: bool, options: '' }
    ]
  }
]
```

When rendering field lists in JS (e.g. template preview), always iterate `sections → section.fields → field`. Never iterate the top-level array as flat fields — it produces `[object Object]`.

### CDN load order in `app.blade.php` (order matters)
Bootstrap CSS → Icons → DataTables CSS → `public.css` → `@yield('css')` → jQuery → Bootstrap JS → **inline IIFE** (sidebar toggle) → DataTables JS → `public.js` → `@yield('js')`

Chart.js (`chart.umd.min.js`) is **not** in the global layout — load it only in the views that need charts via `@section('js')`, before any chart initialisation code.

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
| H3 | Dashboard Teknisi (admin view) | `dashboard/teknisi-all.blade.php` |
| — | Dashboard Teknisi (my tasks) | `dashboard/teknisi-my.blade.php` |
| H4 | Form Laporan | `laporan/create.blade.php` |
| H5 | Template Builder | `template/index.blade.php` |
| H6 | Laporan Customer | `laporan/customer.blade.php` |

---

## Hard Rules

1. **No `@vite()`, npm, or build step** — CDN + `public/css/` only
2. **No second `#menuToggle` listener** — the IIFE in `app.blade.php` is the only one
3. **DataTables Ajax pattern** — exactly as above; never pass data to the index view
4. **CSS tokens only** — no hardcoded hex in CSS/Blade styles. Exception: Chart.js dataset `backgroundColor` must use hex literals because Chart.js cannot read CSS variables — use the matching hex values (`#1565C0` blue, `#FFA000` amber, `#388E3C` green, `#EF5350` red)
5. **`auth()->user()->can()`** in controller action columns — not `@can`
6. **CDN load order** — must match the sequence above
7. **Mobile-first CSS** — base = mobile, add `@media (min-width: 640px)` and `@media (min-width: 1024px)`
8. **Spatie middleware** — `\Spatie\Permission\Middleware\RoleMiddleware` (no 's' at end of Middleware)
