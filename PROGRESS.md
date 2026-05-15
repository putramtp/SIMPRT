# SIPRT — Development Progress

**Stack:** Laravel 10 · PHP 8.1 · MySQL (`db_siprt`) · Bootstrap 5 CDN · jQuery 3.7.1 CDN · Yajra DataTables · Spatie Permission v6
**Last updated:** 2026-05-15 — **All 5 phases complete. Post-launch fixes & features in progress.**

---

## Recent Changes (2026-05-15)

| File | Change | Type |
|---|---|---|
| `TugasController.php` | Renamed route model binding param `$tuga` → `$tugas` in `show`, `edit`, `update`, `destroy` | 🔴 Bug fix |
| `DummyDataSeeder.php` | Added fixed `sales@siprt.com` and `teknisi@siprt.com` accounts (password: `password`) | ✅ Enhancement |
| `tugas/show.blade.php` | Added `?? "-"` null-safe guards on `customer->name` and `assignee->name` | 🔴 Bug fix |
| `HomeController.php` | Role-based post-login redirect: teknisi → `dashboard.teknisi`, admin/sales → `dashboard.sales` | 🔴 Bug fix |
| `LaporanController.php` | `edit()` task dropdown: admin/sales load all tasks; teknisi load only their assigned tasks | 🔴 Bug fix |
| `RolePermissionSeeder.php` | Removed `view customers` permission from teknisi — customer list now hidden from them | 🔴 Bug fix |
| `users` migration | Added `signature` (longText nullable) column | ✅ Feature |
| `User.php` | Added `signature` to `$fillable` | ✅ Feature |
| `EnsureUserHasSignature.php` | New middleware — redirects teknisi without a saved signature to setup page | ✅ Feature |
| `Kernel.php` | Registered `signature.required` middleware alias | ✅ Feature |
| `ProfileController.php` | New — `showSignatureSetup()` + `storeSignature()` | ✅ Feature |
| `profile/signature.blade.php` | New — one-time signature setup page (updatable anytime) | ✅ Feature |
| `routes/web.php` | Profile signature routes + `signature.required` applied to all feature routes | ✅ Feature |
| `laporan/create.blade.php` | SigPad accepts `initialSig`; teknisi signature pre-loaded from `users.signature` | ✅ Feature |

---

## Next Steps & Recommendations

### Priority order

```
1. Smoke test all 3 roles  →  2. Deploy  →  3. Wire up mail  →  4. Decide on template rendering
```

### 1. Manual smoke test before real users (30 min)

Walk through each role end-to-end:
- **Sales:** create customer → create task → assign teknisi → open customer signed link → verify it opens without login
- **Teknisi:** first login → signature setup page → draw signature → redirected to dashboard → see only own tasks → submit report → signature auto-filled → view report → download PDF
- **Admin:** edit a user's role → edit someone else's laporan → delete a task

### 2. Deploy

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

### 3. Configure mail

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

### 4. Wire templates to the report form (medium effort)

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
| `TugasController` route model binding `$tuga` vs `$tugas` | ✅ Fixed 2026-05-15 | Was silently breaking `show`, `edit`, `update`, `destroy` |
| `laporan/edit` task dropdown empty for admin/sales | ✅ Fixed 2026-05-15 | Admin/sales load all tasks; teknisi load only their own |
| Teknisi redirected to sales dashboard on login | ✅ Fixed 2026-05-15 | `HomeController` now does role-based redirect |
| Teknisi could see customer list | ✅ Fixed 2026-05-15 | Removed `view customers` permission from teknisi role |
| No user signature on reports | ✅ Fixed 2026-05-15 | One-time setup on first login; auto-filled in report form |
| Templates not rendered in laporan form | 🟠 Feature gap | Saved to DB but never loaded for teknisi |
| No email on task assignment | 🟡 UX | Pusher only works when app is open |
| No formal report approval workflow | 🟡 UX | `approved` status exists but no UI to trigger it |
| Photos lost on redeploy | 🟡 Ops | Move to S3/Cloudflare R2 for production |
| API login not rate-limited | 🟡 Security | Add `throttle:5,1` to `POST /api/auth/login` |

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
- Fixed accounts: `admin@siprt.com`, `sales@siprt.com`, `teknisi@siprt.com` — all use `password`
- Teknisi: redirected to `dashboard.teknisi` on login; blocked from customer list; must set signature on first login

### Database (all migrated)
`users` (+ `signature`) · `customers` · `tasks` · `reports` (+ `signature_tech/cust`) · `technicians` · `templates` · `push_subscriptions` · Spatie RBAC tables · `personal_access_tokens`

### Controllers
| Controller | Notes |
|---|---|
| `DashboardController` | `sales()`, `teknisi()` — KPI stats, tech status |
| `TugasController` | Full CRUD; `role:admin|sales` for create/edit/delete; Pusher dispatch on create |
| `LaporanController` | Full CRUD; `role:teknisi` for create; ownership check on edit/update/destroy; PDF download |
| `CustomerController` | Full CRUD; `laporan()` with 30-day `temporarySignedRoute`; `publicLaporan()` (no auth) |
| `UserController` | Full CRUD; `can:view users` gate |
| `TemplateController` | JSON store/show/destroy; `role:admin|sales` for write ops |
| `ProfileController` | `showSignatureSetup()`, `storeSignature()` — one-time signature setup |
| `Api/AuthController` | `login` (returns token), `logout`, `me` |
| `Api/TaskController` | `index` (teknisi filtered), `show` |
| `Api/ReportController` | `index`, `show`, `store` (base64 photo); `role:teknisi` |

### Key Views
| View | Features |
|---|---|
| `dashboard/sales.blade.php` | KPI cards, DataTables tasks, desktop right panel |
| `dashboard/teknisi.blade.php` | Own task list + JS detail panel |
| `tugas/create.blade.php` | SplitPane, live summary card |
| `laporan/create.blade.php` | SplitPane, drag-drop, dual signature canvas (tech pre-filled), offline sync |
| `laporan/show.blade.php` | Magazine layout, lightbox, Download PDF, print |
| `laporan/customer.blade.php` | Card grid, detail modal, signed-URL share modal |
| `laporan/customer_public.blade.php` | Public layout (no auth), read-only |
| `laporan/pdf.blade.php` | dompdf A4 template |
| `template/index.blade.php` | 3-col builder (palette / canvas / property panel), DB save/load/delete |
| `profile/signature.blade.php` | One-time signature setup; signature auto-used in all reports |
| `offline.blade.php` | Standalone, no auth |

### Middleware
| Alias | Class | Purpose |
|---|---|---|
| `signature.required` | `EnsureUserHasSignature` | Redirect teknisi without saved signature to setup page |

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
