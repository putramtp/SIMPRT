# CLAUDE.md

Guidance for Claude Code when working in this repository.

---

## Project

**SIPRT** — Laravel 10 task assignment & technician reporting system.
**Stack:** Laravel 10, PHP 8.1+, MySQL (`db_siprt`), Bootstrap 5 CDN, jQuery 3.7.1 CDN, Yajra DataTables, Spatie Permission v6, barryvdh/laravel-dompdf, Laravel Sanctum.
**All 5 development phases complete + post-phase improvements (latest: multi-photo upload, template fields on laporan form, draft/submit flow — 2026-06-02). Next: deployment.**

### Actors

| Role | Can do |
|---|---|
| `admin` / `sales` | Create tasks, manage customers/users, build templates, view all reports |
| `teknisi` | View assigned tasks, submit reports |
| `customer` | Login via `/customer/login` → `customer.dashboard`; see only their company's reports; stored in `customer_users` table with separate `customer` guard |
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
- `can:view users` — users resource
- `can:view customers` — customers resource + laporan
- `GET /c/{customer}/laporan` — public signed URL (no auth)
- `GET /api/*` — Sanctum token auth (see `routes/api.php`)

**Customer portal routes** (separate `auth:customer` guard — prefix `/customer`):
- `GET /customer/login` → `CustomerLoginController@showLoginForm` — separate login page (no staff overlap)
- `POST /customer/login` / `POST /customer/logout` — authenticate/deauthenticate against `customer` guard
- `GET /customer/dashboard` → `CustomerDashboardController@index`
- `GET /customer/laporan` / `GET /customer/laporan/{laporan}` — customer's own reports only
- `GET/POST /customer/profile/signature` + `/customer/profile/password` — outside signature gate

**Teknisi dashboards:**
- `/dashboard/teknisi/all` → `DashboardController@teknisiAll` (admin/sales) — all tasks, DataTables Ajax + Chart.js charts
- `/dashboard/teknisi/my`  → `DashboardController@teknisiMy`  (teknisi)      — own tasks, mobile-first card layout

**Notification routes** (teknisi, admin, sales — inside auth middleware):
- `GET /notifications` → `NotificationController@index` — returns last 20 + unread count as JSON
- `POST /notifications/{id}/read` → `NotificationController@markRead`
- `POST /notifications/read-all` → `NotificationController@markAllRead`

**Task status transition route** (teknisi only):
- `PATCH /tugas/{tugas}/start` → `TugasController@start` — `pending` → `in_progress`; must be declared BEFORE `tugas/{tugas}/edit`

**Profile routes** (all auth users):
- `GET/POST /profile/password` → `ProfileController` — change password
- `GET/POST /profile/signature` → `ProfileController` — manage signature; first-login gate applies to **all roles**

**Route ordering rule:** explicit paths (`tugas/create`, `laporan/create`, `tugas/{tugas}/start`) must be declared BEFORE wildcard routes (`tugas/{tugas}`, `laporan/{laporan}`) or Laravel will match them as model IDs.

### RBAC
Spatie v6 — middleware aliases in `Kernel.php` use `\Spatie\Permission\Middleware\` (no trailing 's').

Permissions: `view/create/edit/delete users`, `assign roles`, `view/create/edit/delete customers`, `view customer reports`.

Fixed seeded accounts (password: `password`):
- `admin@siprt.com` — role: admin
- `sales@siprt.com` — role: sales
- `teknisi@siprt.com` — role: teknisi
- `customer@siprt.com` — in `customer_users` table (no Spatie role), linked to PT Maju Jaya Abadi; login at `/customer/login`

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
Bootstrap CSS → Icons → DataTables CSS → `public.css` → `@yield('css')` → jQuery → Bootstrap JS → **inline IIFE** (sidebar toggle + user menu toggle) → DataTables JS → `public.js` → `@yield('js')` → notification JS (teknisi, admin, sales)

Chart.js (`chart.umd.min.js`) is **not** in the global layout — load it only in the views that need charts via `@section('js')`, before any chart initialisation code.

### Task assignees (many-to-many)
Tasks support multiple assignees via a `task_user` pivot table (task_id, user_id, timestamps).

- `Task::assignees()` — `belongsToMany(User::class, 'task_user')->withTimestamps()`
- `User::tasks()` — `belongsToMany(Task::class, 'task_user')->withTimestamps()` (used for `withCount` in dashboards)
- `TugasController@store/update` validates `assignees` (array of user IDs), then calls `$task->assignees()->sync($ids)`
- `TugasController@start` checks `$tugas->assignees()->where('user_id', Auth::id())->exists()`
- `LaporanController@create/edit` filters tasks with `Task::whereHas('assignees', fn($q) => $q->where('users.id', Auth::id()))`
- `Api\TaskController` and `Api\ReportController` use the same pivot check for teknisi authorization
- `create.blade.php` — teknisi cards are multi-selectable (toggle); hidden inputs `assignees[]` managed dynamically by `toggleTeknisi()`
- `edit.blade.php` — checkboxes `name="assignees[]"` pre-checked from `$selectedTeknisi`
- DataTables `assignee_name` column uses `$t->assignees->pluck('name')->join(', ')` (computed, not orderable/searchable)

### Laporan (Report) — key behaviour

**`reports` table columns:** `task_id`, `user_id`, `description`, `status` (draft|submitted|approved), `photos` (JSON array of paths), `signature_cust` (file path string), `template_data` (JSON `{field_id: value}`), timestamps. No `photo` (singular) or `signature_tech` columns — both removed by migrations `2026_06_02_000002` and `2026_06_02_000003`.

**Signatures:**
- `signature_tech` is gone from `reports` — the technician's signature is read at display time from `$laporan->teknisi->signature` (stored in `users.signature` as a file path). Never save tech sig on the report.
- `signature_cust` stores a **file path** (e.g. `signatures/{sha256}.png`), never a base64 string. Saving is handled by `LaporanController::saveSig(string $base64): string` — decodes base64, writes PNG to `storage/app/public/signatures/`, returns the path. Display: `asset('storage/' . $laporan->signature_cust)`. PDF: `public_path('storage/' . $laporan->signature_cust)`.

**Status flow:**
- `LaporanController@store`: if `signature_cust` is present → `status = submitted`, task → `completed`, `TaskCompletedNotification` sent. If absent → `status = draft`, task stays `in_progress`, no notification.
- `LaporanController@update`: if `signature_cust` is added to a `draft` → auto-sets `status = submitted`, marks task `completed`, sends notification.
- `LaporanController@create`: if a draft already exists for the requested `task_id`, redirects to `laporan.edit` for that draft instead of showing the create form.

**Dashboard (`teknisiMy`):** active tasks = `pending/in_progress` tasks with no `submitted` report from the current user. Tasks with a `draft` report stay in the active list with an orange bar + "Draft" badge + "Lengkapi" button → `laporan.edit`. `DashboardController@teknisiMy` eager-loads `reports` scoped to `where('user_id', $userId)` so the view can check per-task report state.

**Multi-photo upload:** `photos[]` multiple file input; stored as JSON array in `photos` column. Up to 10 photos. `laporan/show.blade.php` and `customer/laporan/show.blade.php` render a flex grid (1 photo = full width, 2+ = 50/50). PDF renders all photos sequentially. Thumbnail in list/card views uses `$report->photos[0]`. Edit view shows existing thumbnails and accepts new `photos[]` to replace them.

**Template fields:** `LaporanController@create` eager-loads `task.template` and passes `$taskTemplates` (keyed by task ID) as JSON. When a task is selected, JS dynamically renders a "Formulir Tugas" card from the template's `sections → fields` structure. Supported types: `text`, `textarea`, `number`, `date`, `checkbox`, `select`; `photo`/`signature` template field types are skipped. Values submitted as `template_data[field_id]` → stored as JSON in `template_data`.

### DB Notifications
- **`TaskAssignedNotification`** → all assignees: new task assigned (`TugasController@store`), only newly added on reassignment (`@update`)
- **`TaskStartedNotification`** → admin+sales: teknisi clicked Mulai Tugas (`TugasController@start`); `teknisi_name` lists all assignees joined with `, `
- **`TaskCompletedNotification`** → admin+sales: teknisi submitted laporan with customer signature (`LaporanController@store` or `@update` when draft → submitted)
- All use `via: ['database']`; stored in `notifications` table
- `NotificationController` returns JSON: `{ notifications: [...], unread: n }`; each item has `id`, `data`, `read` (bool), `time` (human diff)
- Notification data fields by type:
  - `task_assigned`: task_id, title, customer_name, due_date, url → tugas.show
  - `task_started`: type, task_id, title, teknisi_name, customer_name, url → tugas.show
  - `task_completed`: type, task_id, title, teknisi_name, customer_name, url → laporan.show
- Bell + drawer visible to teknisi, admin, and sales; notification routes use `role:teknisi|admin|sales`
- `buildItem()` in `app.blade.php` branches on `d.type`: `task_started` → `ti-player-play`, `task_completed` → `ti-file-check`, default → `ti-clipboard-plus`
- UI: slide-down drawer from topbar (`#notifDrawer`), backdrop (`#notifBackdrop`), orange unread dot on bell badge

### First-login signature gate
- **Staff** (`web` guard): `EnsureUserHasSignature` middleware (alias `signature.required`) redirects to `GET /profile/signature`. Profile routes declared outside the gate group.
- **Customer** (`customer` guard): NOT gated — customers go straight to the dashboard after login. `EnsureCustomerHasSignature` (alias `customer.signature`) still exists in `Kernel.php` but is no longer applied to any route; the signature profile page remains available for customers to set it voluntarily.
- The staff gate checks that `->signature` is non-null/non-empty.

### Sidebar user dropdown
The `sidebar-footer` contains a `#sidebarUserToggle` button (avatar + name + role + chevron). Clicking it toggles `#sidebarUserMenu.open` via `max-height` CSS transition. The menu holds: Tanda Tangan → `/profile/signature`, Edit Password → `/profile/password`, Keluar (logout form). The IIFE in `app.blade.php` handles the toggle. The logout button keeps class `sidebar-logout` for the global submit-loading exclusion in `public.js`.

### Global submit loading state
`public/css/public.js` attaches a `submit` event on every `<form>` that disables the submit button and shows a spinner. Excludes `.sidebar-logout` and `.pwa-logout-btn` forms. Restores on `pageshow` with `ev.persisted` (bfcache).

### CSS cache busting
`public.css` and `public.js` are linked with `?v={{ filemtime(public_path(...)) }}`. SW cache name is bumped whenever `public.css` changes (currently `siprt-v4`).

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
4. **CSS tokens only** — no hardcoded hex in CSS/Blade styles. Exception: Chart.js dataset `backgroundColor` and notification drawer inline styles must use hex literals (cannot read CSS vars at runtime)
5. **`auth()->user()->can()`** in controller action columns — not `@can`
6. **CDN load order** — must match the sequence above
7. **Mobile-first CSS** — base = mobile, add `@media (min-width: 640px)` and `@media (min-width: 1024px)`
8. **Spatie middleware** — `\Spatie\Permission\Middleware\RoleMiddleware` (no 's' at end of Middleware)
9. **Bump SW cache name** (`siprt-vN` in `public/sw.js`) whenever `public.css` changes significantly — prevents stale cached CSS from poisoning the app shell
10. **Notification drawer inline styles** — `#notifDrawer` and `buildItem()` in `app.blade.php` use inline styles deliberately (cache-immune fallback); do not move to CSS classes without testing SW cache behavior
