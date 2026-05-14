# SIPRT — Development Progress

**Stack:** Laravel 10 · PHP 8.1 · MySQL (`db_siprt`) · Bootstrap 5 CDN · jQuery 3.7.1 CDN · Yajra DataTables · Spatie Permission v6
**Last updated:** 2026-05-14 — **All 5 phases complete. Ready for deployment.**

---

## Next Steps & Recommendations

### Priority order

```
1. Fix laporan/edit task query bug  →  2. Smoke test all 3 roles  →  3. Deploy  →  4. Wire up mail  →  5. Decide on template rendering
```

### 1. Fix known bug — `laporan/edit` task dropdown empty for admin/sales (15 min)

`LaporanController@edit` queries tasks with `where('assigned_to', Auth::id())`. When an admin or sales user edits a report, that filter returns nothing because they are not the assigned technician.

**Fix:** remove the ownership filter and load all tasks instead:
```php
// LaporanController@edit — replace:
$tasks = Task::where('assigned_to', Auth::id())->with('customer')->get();
// with:
$tasks = Task::with('customer')->get();
```

### 2. Manual smoke test before real users (30 min)

Walk through each role end-to-end:
- **Sales:** create customer → create task → assign teknisi → open customer signed link → copy share URL → verify it opens without login
- **Teknisi:** see task in dashboard → submit report with photo + signature → view report → download PDF
- **Admin:** edit a user's role → edit someone else's laporan → delete a task

### 3. Deploy

Minimum viable deployment:
1. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://yourdomain.com` in `.env`
2. `php artisan migrate --force`
3. `php artisan db:seed --class=RolePermissionSeeder`
4. `php artisan storage:link`
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`

Hosting options (cheapest → managed):
- **VPS** (DigitalOcean, Vultr, Hetzner) + Nginx — full control, manual setup
- **Laravel Forge** — provisions and deploys automatically from Git
- **Railway / Render** — push-to-deploy, free tier available

### 4. Configure mail

Password reset is broken without mail. Add to `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org   # or smtp.resend.com, smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_key
MAIL_PASSWORD=your_secret
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=SIPRT
```

**Bonus:** add email notification when a task is assigned — teknisi will miss tasks if Pusher isn't open.

### 5. Wire templates to the report form (medium effort)

The template builder saves field definitions to the `templates` table, but `laporan/create.blade.php` never loads them — teknisi still sees the plain form. If templates are core to the workflow, this needs to be built before launch.

Rough approach:
- Add a template selector dropdown to the laporan create form
- On selection, fetch the template fields via `GET /template/{id}` (already returns JSON)
- Dynamically render the custom fields below the standard description field
- Serialize custom field values into the `description` column as JSON (or add a `fields` JSON column to `reports`)

---

## Known Gaps

| Issue | Severity | Notes |
|---|---|---|
| `laporan/edit` task dropdown empty for admin/sales | 🔴 Bug | Fix in `LaporanController@edit` — see above |
| Templates not rendered in laporan form | 🟠 Feature gap | Saved to DB but never loaded for teknisi |
| No email on task assignment | 🟡 UX | Pusher only works when app is open |
| No formal report approval workflow | 🟡 UX | `approved` status exists but no UI to trigger it |
| Photos lost on redeploy | 🟡 Ops | Move to S3/Cloudflare R2 for production |
| API login not rate-limited | 🟡 Security | Add `throttle:5,1` to `POST /api/auth/login` |

---

---

## Status

| Phase | Status | Summary |
|---|---|---|
| 0 — Scaffold | ✅ | DB, models, controllers, seeders, factories, RBAC |
| 1 — Responsive CSS | ✅ | 3-breakpoint system (640/1024px), 12 layout components |
| 2 — Interactive UI | ✅ | Lightbox, drag-drop upload, dual signature canvas, magazine layout, print |
| 3 — Backend | ✅ | Full CRUD, DataTables Ajax, signed URLs, template DB, PWA SW |
| 4 — PWA | ✅ | Manifest, service worker, Pusher notifications, offline/background sync |
| 5 — Production | ✅ | Security hardening, PDF export, Sanctum API, 24 PHPUnit tests |

---

## What's Built

### Auth & RBAC
- Roles: `admin`, `sales`, `teknisi`
- Spatie middleware: `role:admin|sales`, `role:teknisi`, `can:view users`, `can:view customers`
- Fixed admin: `admin@siprt.com` / `password`

### Database (all migrated)
`users` · `customers` · `tasks` · `reports` (+ `signature_tech/cust`) · `technicians` · `templates` · `push_subscriptions` · Spatie RBAC tables · `personal_access_tokens`

### Controllers
| Controller | Notes |
|---|---|
| `DashboardController` | `sales()`, `teknisi()` — KPI stats, tech status |
| `TugasController` | Full CRUD; `role:admin|sales` for create/edit/delete; Pusher dispatch on create |
| `LaporanController` | Full CRUD; `role:teknisi` for create; ownership check on edit/update/destroy; PDF download |
| `CustomerController` | Full CRUD; `laporan()` with 30-day `temporarySignedRoute`; `publicLaporan()` (no auth) |
| `UserController` | Full CRUD; `can:view users` gate |
| `TemplateController` | JSON store/show/destroy; `role:admin|sales` for write ops |
| `Api/AuthController` | `login` (returns token), `logout`, `me` |
| `Api/TaskController` | `index` (teknisi filtered), `show` |
| `Api/ReportController` | `index`, `show`, `store` (base64 photo); `role:teknisi` |

### Key Views
| View | Features |
|---|---|
| `dashboard/sales.blade.php` | KPI cards, DataTables tasks, desktop right panel |
| `dashboard/teknisi.blade.php` | Task list + JS detail panel |
| `tugas/create.blade.php` | SplitPane, live summary card |
| `laporan/create.blade.php` | SplitPane, drag-drop, dual signature canvas, offline background sync |
| `laporan/show.blade.php` | Magazine layout, lightbox, Download PDF, print |
| `laporan/customer.blade.php` | Card grid, detail modal, signed-URL share modal |
| `laporan/customer_public.blade.php` | Public layout (no auth), read-only |
| `laporan/pdf.blade.php` | dompdf A4 template |
| `template/index.blade.php` | 3-col builder (palette / canvas / property panel), DB save/load/delete |
| `offline.blade.php` | Standalone, no auth |

### PWA
- `public/favicon/site.webmanifest` — display: standalone, shortcuts
- `public/sw.js` — cache-first static, network-first navigation, offline fallback, background sync (`laporan-sync`)
- Pusher real-time: `TaskAssigned` event → teknisi notification bell + toast (configure `.env`)

### API (`/api/*`)
```
POST   /api/auth/login       public
POST   /api/auth/logout      auth:sanctum
GET    /api/auth/me          auth:sanctum
GET    /api/tasks            auth:sanctum (teknisi sees own only)
GET    /api/tasks/{task}     auth:sanctum
GET    /api/reports          auth:sanctum
GET    /api/reports/{report} auth:sanctum
POST   /api/reports          auth:sanctum + role:teknisi
```

### Tests — 24 pass
`TugasTest` · `LaporanTest` · `PublicLaporanTest` · `Api/AuthTest`

---

## Deployment Checklist

- [ ] Configure `.env` — `APP_URL`, `DB_*`, `MAIL_*`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --class=RolePermissionSeeder`
- [ ] `php artisan storage:link`
- [ ] Configure Pusher `.env` keys (optional — app degrades gracefully without it)
- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Queue worker for broadcast events (`php artisan queue:work`)

---

## Key Conventions

1. **No Vite / npm** — CDN + `public/css/public.css` / `public/css/public.js`
2. **Single `#menuToggle` listener** — IIFE in `layouts/app.blade.php` only
3. **DataTables Ajax** — `index()` returns JSON on Ajax, view on non-Ajax; always `addIndexColumn()` + `rawColumns()`
4. **CSS tokens only** — never hardcode hex; use `--blue`, `--text`, etc.
5. **`auth()->user()->can()`** in controller action columns — not `@can` Blade
6. **CDN order** — jQuery → Bootstrap JS → inline IIFE → DataTables → `public.js` → `@yield('js')`
7. **Mobile-first** — base styles mobile, layer up with `@media (min-width: 640px)` / `@media (min-width: 1024px)`
8. **Spatie middleware** — `\Spatie\Permission\Middleware\RoleMiddleware` (no trailing 's')
