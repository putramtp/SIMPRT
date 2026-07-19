# SIPRT — Progress Log

## Phase 1: Authentication & RBAC ✅
- Laravel Breeze scaffolding stripped to custom auth (login/logout only)
- Spatie Permission v6 integrated; `RolePermissionSeeder` seeds 4 roles + all permissions
- Middleware aliases registered in `Kernel.php` (`role`, `permission`, `can`)
- First-login gate: all roles redirected to `/profile/signature` until signature is set (`EnsureUserHasSignature` middleware)
- Fixed seeded accounts: admin, sales, teknisi, customer (all password: `password`)
- Customer role linked via `users.customer_id` → `customers.id`

## Phase 2: Core Models & CRUD ✅
- Models: `User`, `Customer`, `Tugas`, `Laporan`, `Template`
- CRUD for Users, Customers, Templates (admin/sales only)
- Yajra DataTables Ajax pattern established for all index pages
- Spatie Permission RBAC wired to all routes and action buttons
- `templates.fields` stored as JSON array of sections (not flat fields)

## Phase 3: Task Assignment Flow ✅
- `Tugas` model with status enum: `pending` → `in_progress` → `done`
- Task create/edit (admin/sales); assign teknisi + customer + due date
- `PATCH /tugas/{tugas}/start` route — Mulai Tugas button (teknisi only, pending→in_progress)
- Route ordering enforced: explicit paths before wildcard `{tugas}` param
- Dashboard Sales: DataTables Ajax + Chart.js status breakdown chart
- Dashboard Teknisi All (admin/sales view): DataTables Ajax + Chart.js
- Dashboard Teknisi My (teknisi view): mobile-first card layout, 1:1:1 action buttons

## Phase 4: Reporting & PDF ✅
- `Laporan` create form: description, signature pad (technician + customer), photo upload
- PDF generation via barryvdh/laravel-dompdf; embedded base64 signatures + photo
- Public signed URL (`/c/{customer}/laporan`) — no auth required, HMAC-signed
- Customer dashboard: filtered to own company's reports only
- Template builder: drag-and-drop section/field builder, JSON stored in DB

## Phase 5: PWA & Offline ✅
- Service worker (`public/sw.js`) with cache-first static + network-first navigation
- App manifest (`public/manifest.json`) — installable on Android/iOS
- IndexedDB queue for offline laporan submission (`laporan-queue` store)
- Background sync (`laporan-sync` tag) — retries queued reports when online
- Offline fallback page (`/offline`)
- SW cache name versioned (`siprt-v1` → `siprt-v2` → `siprt-v3` → `siprt-v4`)

## Post-Phase Improvements (2026-05-20) ✅

### Profile Sidebar Links (later moved to sidebar-user dropdown)
- "Edit Password" and "Tanda Tangan" links added to sidebar for all authenticated roles
- `ProfileController` handles GET+POST for `/profile/password` and `/profile/signature`

### DB Notifications (Teknisi)
- `App\Notifications\TaskAssignedNotification` via `['database']` channel
- Triggered on `TugasController@store` (new task) and `@update` (reassignment only)
- `NotificationController`: `index` (JSON, last 20), `markRead`, `markAllRead`
- UI: slide-down drawer from topbar (`#notifDrawer` + `#notifBackdrop`)
- Orange unread dot (`#FF6B35`) on bell icon; `#notifCountLabel` shows unread count
- Drawer HTML and `buildItem()` use inline styles as cache-immune fallback
- Auto-polls every 60 seconds when drawer is closed

### Task Status Transition (Mulai Tugas)
- `PATCH /tugas/{tugas}/start` → `TugasController@start`
- Validates teknisi owns the task and status is `pending`
- Card in teknisi-my shows "Mulai Tugas" button only when `status === pending`
- On success redirects back with flash message

### Teknisi Dashboard Card Actions (1:1:1 Layout)
- Three action buttons in equal columns: Mulai Tugas / Buat Laporan / Detail
- Button visibility gated by task status and role
- Mobile-first: full-width stack on mobile, row on tablet+

### Global Submit Loading State
- `public/css/public.js`: `submit` event on all `<form>` elements
- Disables submit button, shows Bootstrap spinner inside button text
- Excludes `.sidebar-logout` and `.pwa-logout-btn` forms (logout must not be blocked)
- Restores button on `pageshow` with `ev.persisted` (bfcache back-navigation)

### CSS Cache Busting
- `public.css` and `public.js` linked with `?v={{ filemtime(public_path(...)) }}`
- Prevents browsers from serving stale CSS after updates
- SW cache name bumped to `siprt-v4` (current) after significant CSS changes

### Reset Password
- Admin can reset any user's password from the users index
- Generates a secure random password, emails it to the user
- Uses Laravel's built-in password reset notification

### Signature Required for All Roles (2026-05-21)
- `EnsureUserHasSignature` middleware: removed `hasRole('teknisi')` check — all roles now redirected to `/profile/signature` on first login if no signature set
- "Tanda Tangan" sidebar link now visible to all roles including customer

### Sidebar User Dropdown (2026-05-21)
- Sidebar footer replaced static user card + separate logout button with a clickable toggle
- Clicking the user card (with chevron) expands a collapsible menu: Tanda Tangan, Edit Password, Keluar
- Profile nav links removed from both sidebar nav branches (customer and others)
- Implemented with `max-height: 0 → 200px` CSS transition; toggled by `#sidebarUserToggle` in the IIFE
- SW cache bumped to `siprt-v4` for the CSS change

### Admin/Sales Notifications for Task Events (2026-05-21)
- `TaskStartedNotification` — notifies all admin+sales when teknisi clicks Mulai Tugas
- `TaskCompletedNotification` — notifies all admin+sales when teknisi submits laporan
- Notification bell + drawer extended to admin+sales (was teknisi-only)
- `buildItem()` renders different icon+label per `d.type`: play (started), file-check (completed), clipboard (assigned)
- Notification routes changed from `role:teknisi` to `role:teknisi|admin|sales`
- URL for task_started → `tugas.show`; for task_completed → `laporan.show`

### Notification Drawer Redesign (2026-05-21)
- Replaced old `position:fixed` dropdown with full-width slide-down drawer
- Design matches reference mockup (`dashboard_teknisi_notifikasi.html`)
- Drawer: dark blue header (`#0D47A1`), unread items `#F0F7FF`, read items `#fff`
- Animation: `opacity 0→1` + `translateY(-12px→0)` at `z-index: 9998`
- Backdrop at `z-index: 9997`; clicking backdrop closes drawer
- Desktop: right-aligned, `width: 360px`; mobile: full-width
- Footer link: "Lihat semua notifikasi ↗" → `/laporan`

## Multi-Assignee Tasks (2026-05-26) ✅

### Pivot Table
- Replaced `tasks.assigned_to` FK column with `task_user` pivot table (task_id, user_id, timestamps)
- Migration `2026_05_26_000003_create_task_user_pivot.php`: creates pivot, migrates existing data, drops old column
- `Task::assignees()` — `belongsToMany(User::class, 'task_user')`
- `User::tasks()` changed from `hasMany(..., 'assigned_to')` to `belongsToMany(Task::class, 'task_user')` — `withCount` in dashboards continues to work via pivot JOIN

### Controller Updates
- `TugasController@store/update`: validates `assignees[]` array, uses `sync()`; notifies all assignees on create, only new ones on update
- `TugasController@start`: checks `$tugas->assignees()->where('user_id', Auth::id())->exists()`
- `TugasController@index/show/edit`: loads `assignees` relation; `assignee_name` DataTables column joins names with `, `
- `DashboardController@teknisiMy`: `whereHas('assignees', ...)` instead of `where('assigned_to', ...)`
- `DashboardController@teknisiAll`: loads `assignees`; added missing `use App\Models\Report`
- `LaporanController@create/edit`: `whereHas('assignees', ...)` filter for teknisi
- `Api\TaskController`: pivot-based filtering and authorization; response includes `assignees` array
- `Api\ReportController`: pivot check replaces `assigned_to !== $user->id`
- `TaskAssigned` event: broadcasts on all assignees' private channels
- `TaskStartedNotification`: `teknisi_name` shows all assignees joined with `, `

### View Updates
- `tugas/create.blade.php`: teknisi cards are now multi-selectable (toggle, not single-select); JS manages `assignees[]` hidden inputs dynamically via `toggleTeknisi()`; step 2 validation checks `$('.ftg-tek-card.selected').length`; summary shows comma-joined names
- `tugas/edit.blade.php`: replaced single `<select name="assigned_to">` with checkboxes `name="assignees[]"` pre-checked from `$selectedTeknisi`
- `tugas/show.blade.php`: lists all assignees joined with `, `
- DataTables columns for assignee_name: set `orderable: false, searchable: false` (computed column)

### Factory
- `TaskFactory`: removed `assigned_to`; uses `afterCreating` callback to attach a random teknisi via `sync()`

---

## Customer Portal Separation (2026-05-26) ✅

### Separate `customer_users` Table
- New `customer_users` table (id, customer_id FK, name, email, password, signature, remember_token, timestamps)
- New `CustomerUser` model (`app/Models/CustomerUser.php`) — Authenticatable, no Spatie roles
- Data migration: moves existing customer-role users from `users` → `customer_users` on upgrade; reverses on rollback
- `customer` Spatie role removed; `users` table no longer has customer accounts

### Separate Laravel Guard
- `customer` guard added to `config/auth.php` using `customer_users` provider + `CustomerUser` model
- `EnsureCustomerHasSignature` middleware (alias `customer.signature`) — guard-specific signature gate

### Separate Customer Login Page
- `GET /customer/login` → `CustomerLoginController@showLoginForm` → `customer.auth.login` view
- `POST /customer/login` → authenticates against `customer` guard
- `POST /customer/logout` → logs out `customer` guard, redirects to `/customer/login`
- Login page: same visual style as staff login; "Staff login →" link to avoid confusion
- Staff login at `/login` unchanged and exclusive to admin/sales/teknisi

### Customer Layout & Views
- New `resources/views/layouts/customer.blade.php` — simplified sidebar (Beranda, Laporan Saya), user dropdown (Tanda Tangan, Edit Password, Keluar), mobile bottom nav; no notification bell
- `customer.dashboard` → `CustomerDashboardController@index` (extends `layouts.customer`)
- `customer.laporan` → `CustomerDashboardController@laporan`
- `customer.laporan.show` → `CustomerDashboardController@show` (with authorization: report must belong to user's customer)
- `customer.profile.signature` + `customer.profile.password` → `CustomerProfileController`

### Portal Access Management (Admin UI)
- `Customer@portalUser` — `hasOne(CustomerUser::class)` relationship
- `customers/{customer}/portal-user` (POST) → `CustomerController@storePortalUser` — create portal account
- `customers/{customer}/portal-user/reset-password` (POST) → `CustomerController@resetPortalPassword`
- Customer `show` view: "Portal Akses" card shows existing email + reset-password accordion, or create form if no portal user

### Cleaned Up
- `app.blade.php`: removed customer sidebar/bottom-nav branches (customer uses separate layout)
- `HomeController`: removed customer role redirect (customer guard never hits `/home`)
- `UserController`: removed customer role option and customer_id field
- `User` model: removed `customer_id` fillable and `customer()` relationship
- `DashboardController`: removed `customer()` method (replaced by `CustomerDashboardController`)
- `RolePermissionSeeder`: removed customer role

---

## Template Fields on Laporan Form + Multi-Photo Upload (2026-06-02) ✅

### Template Fields on Technician Form
- `LaporanController@create`: eager-loads `task.template`; passes `$taskTemplates` (JSON, keyed by task ID) to view
- `laporan/create.blade.php`: when a task is selected, JS reads `taskTemplates[taskId]` and dynamically renders a "Formulir Tugas" card with all sections and fields from the template
- Supported field types: `text`, `textarea`, `number`, `date`, `checkbox` (hidden+checkbox pair), `select` (options split by `\n` or `,`); `photo` and `signature` type fields are skipped (handled by dedicated sections)
- Inputs named `template_data[field_id]`; submitted as `template_data` array
- `LaporanController@store`: validates `template_data` as nullable array; stores in `template_data` JSON column
- `laporan/show.blade.php`: "Formulir Tugas" card renders each field label + stored value; checkboxes shown as ✓/✗
- `laporan/pdf.blade.php`: template data rendered as an `info-table` before photos section

### Multiple Photo Upload
- Migration `2026_06_02_000001_add_photos_template_data_to_reports_table`: adds `photos` (JSON) and `template_data` (JSON) columns to `reports` table
- `Report` model: `photos` and `template_data` added to `$fillable`; cast as `array`
- `laporan/create.blade.php`: photo input changed to `name="photos[]" multiple`; drag-drop zone accepts multiple files; thumbnails rendered in a flex grid with individual × remove buttons (up to 10 photos); file management via `filesArr` + `DataTransfer` + `syncFileInput()`
- **Bug fixed**: in the `change` handler, `this.value = ''` was executing AFTER `syncFileInput()` had set the DataTransfer files, clearing them before form submission; fixed by capturing `Array.from(this.files)` first, then clearing, then pushing to `filesArr`
- `LaporanController@store`: iterates `$request->file('photos')`, stores each to `laporan/` disk, saves JSON path array to `photos` column
- `laporan/show.blade.php`: photos grid — single photo = full width 260px, multiple = 50/50 grid 140px; each photo clickable with lightbox; falls back to legacy `photo` column for old records
- `laporan/pdf.blade.php`: all photos rendered sequentially; falls back to legacy `photo` column

---

## Consolidate `photo` → `photos`, Drop Single Column (2026-06-02) ✅

- Migration `2026_06_02_000002_migrate_photo_to_photos_drop_column`: data-migrates existing `reports.photo` values into `photos` JSON array (`["path"]`), then drops the `photo` column entirely
- `Report` model: removed `photo` from `$fillable`; only `photos` (cast: array) remains
- `LaporanController@update`: accepts `photos[]` multiple, replaces existing `photos` array
- `Api/ReportController`: `photo_url` in index/show returns `$r->photos[0]`; base64 store saves as `photos: [filename]`
- `ReportFactory`: `photo => null` → `photos => null`
- All views updated — no `->photo` references remain:
  - `laporan/show.blade.php`, `laporan/pdf.blade.php`, `customer/laporan/show.blade.php`: `$allPhotos = $laporan->photos ?? []`
  - `laporan/edit.blade.php`: shows current photo thumbnails + `photos[]` multiple input
  - Card/list thumbnails (customer dashboard, laporan index, customer_public): use `$report->photos[0]`

---

## Task Auto-Complete + Dashboard Draft State (2026-06-02) ✅

### Task Status Auto-Complete
- `LaporanController@store`: after creating a submitted laporan, calls `Task::where('id', ...)->update(['status' => 'completed'])`
- `LaporanController@create`: only excludes tasks with a **submitted** report from the dropdown (`whereNotIn` on submitted task IDs); if a **draft** exists for the requested `task_id`, redirects to `laporan.edit` for that draft
- `LaporanController@update`: if draft report gets `signature_cust` added → auto-sets `status = submitted`, marks task `completed`, sends `TaskCompletedNotification`

### Dashboard Teknisi/My — Draft Awareness
- `DashboardController@teknisiMy`: eager-loads `reports` filtered to `where('user_id', $userId)` alongside each task
- Active tasks filter: excludes tasks with a **submitted** report from current user (draft tasks remain visible)
- Task cards for draft laporan: orange bar (`bar-orange`), `badge-draft` badge, **"Lengkapi"** button → `laporan.edit` for the draft
- Tasks with submitted laporan: disappear from active list (status = `completed`), appear in "Riwayat Terbaru"

---

## Submit Button Moved to Form Bottom (2026-06-02) ✅

- `laporan/create.blade.php`: removed submit + cancel from inside the "Isi Laporan" card; placed at the bottom of `sp-main` after the signature pads — teknisi fills task, template fields, photos, signatures, then submits

---

## Draft/Submit Flow — Customer Signature Required (2026-06-02) ✅

- `LaporanController@store`: `signature_cust` present → `status = submitted`, task completed, admin/sales notified; `signature_cust` absent → `status = draft`, task stays `in_progress`, info flash message, no notification
- `laporan/edit.blade.php` (draft mode): shows alert banner + customer signature canvas (SigPad); signing + saving triggers auto-submit in `update()` which marks task completed and notifies
- Admin/sales retain full status dropdown in edit view; technician sees only the signature canvas when completing a draft
- `teknisi-my` dashboard: draft tasks show orange "Draft" badge + "Lengkapi" (→ edit) instead of "Isi Laporan"

---

## Signature Refactor — Remove `signature_tech`, Store `signature_cust` as File (2026-06-02) ✅

- Migration `2026_06_02_000003_refactor_report_signatures`:
  - Converts any existing base64 `signature_cust` DB values → PNG files in `storage/app/public/signatures/` (SHA-256 hash of image data as filename; duplicate signatures reuse the same file)
  - Drops `signature_tech` column from `reports` table
- `Report` model: `signature_tech` removed from `$fillable`; `signature_cust` now stores a file path, not base64
- `LaporanController::saveSig(string $base64): string` — private helper that decodes base64, writes to `storage/public/signatures/{sha256}.png`, returns the path; called by both `store()` and `update()`
- `laporan/create.blade.php`: tech signature canvas removed entirely — only customer signature canvas remains (section renamed "Tanda Tangan Customer"); `h_sig_tech` hidden input and all `sigTech` JS references removed; `$userSignature` no longer passed from controller
- Display in all views (show, customer/laporan/show, edit): tech sig → `asset('storage/' . $laporan->teknisi->signature)`; cust sig → `asset('storage/' . $laporan->signature_cust)`
- PDF: both sigs use `public_path('storage/' . ...)` for dompdf local file access
- `Api/ReportController`: `show()` returns `signature_tech_url` (from teknisi relation) and `signature_cust_url` (from path); `store()` saves `signature_cust` via same file pattern

---

## PDF Laporan Redesign — Professional Layout (2026-06-09) ✅

- `laporan/pdf.blade.php` fully redesigned with a professional document structure:
  - **Top accent bar** — 6px solid `#1565C0` stripe anchors the letterhead
  - **Letterhead** — brand left (SIPRT + tagline + date), document title + report number + status badge right
  - **Informasi Umum** — two-column key/value grid: Judul Tugas, Customer, Teknisi Pelaksana (all assignees), Dibuat Laporan Oleh, Deadline, Prioritas (color-coded badge), Status, Kontak Customer (address/phone/email)
  - **Section headers** — white-on-blue solid bars for each section (Deskripsi Pekerjaan, Instruksi Tugas, Formulir, Dokumentasi Foto, Tanda Tangan)
  - **Template field sections** — sub-section titles rendered as light-blue rows inside the template table
  - **Photo grid** — 2-per-row layout with captions; single photo centered at 60% width
  - **Signature block** — two-cell table with role label, signature image, name, designation, and date line under each
  - **Fixed footer** — report number + customer name on every page, separated by a blue top border
- `LaporanController@pdf`: added `task.assignees` to eager-load so all assignees appear in the PDF

---

## Laravel PWA Package — silviolleite/laravelpwa (2026-06-09) ✅

- Installed `silviolleite/laravelpwa ^2.0` via Composer
- Published config, icons, views, and serviceworker assets
- `config/laravelpwa.php` configured with SIPRT branding: name/short_name `SIPRT`, theme `#1565C0`, bg `#f4f6fb`, portrait orientation, shortcuts to `/dashboard/teknisi/my` and `/laporan/create`; icon paths point to existing `public/favicon/` files
- `resources/views/vendor/laravelpwa/meta.blade.php` overridden: SW registration changed from `/serviceworker.js` → `/sw.js` (keeps the existing custom service worker)
- `resources/views/layouts/app.blade.php`:
  - Replaced 5 manual PWA meta tags + `<link rel="manifest">` with single `@laravelPWA` directive
  - Removed duplicate SW registration block at the bottom (now handled by the directive)
- Package adds `/manifest.json` route (dynamic, served from config) and full iOS splash screen `<link>` tags for 10 device sizes
- `resources/views/vendor/laravelpwa/offline.blade.php` updated to `@include('offline')` so both routes show the same page

---

## Offline Page Redesign (2026-06-09) ✅

- `resources/views/offline.blade.php` fully redesigned:
  - **Brand bar** — SIPRT logo + name at the top
  - **Animated icon** — three pulse rings around wifi-off SVG to indicate "searching"
  - **Status badge** — flips from yellow "Tidak Ada Koneksi" to green "Koneksi Tersedia" when network is restored
  - **Feature availability cards** (3 rows): ✓ Cached pages, ✓ Queued offline laporan (auto-synced), ✗ Real-time data & notifications
  - **Smart auto-reconnect**: pings server via `HEAD /?_offline_check=` every 8 seconds; listens to `online`/`offline` browser events; shows toast "Koneksi kembali — mengalihkan…" and auto-reloads after 1.8 s
  - **Retry button** spins while checking, restores on failure
  - **Footer note** with SIPRT branding and "Koneksi dipantau otomatis" info

---

## PWA Icons — SIPRT Logo Applied (2026-06-09) ✅

- All 18 files in `public/images/icons/` replaced with SIPRT logo (`public/favicon/SIPRT.png`) resized via PHP GD:
  - **8 app icons**: `icon-72x72.png` → `icon-512x512.png` — logo scaled to fill square canvas, white background
  - **10 iOS splash screens**: `splash-640x1136.png` → `splash-2048x2732.png` — logo centered on white canvas at exact portrait dimensions
- All icons referenced by `config/laravelpwa.php` and injected via `@laravelPWA` directive

---

## Customer Signature Gate Removed (2026-07-19) ✅

- `routes/web.php`: removed the `customer.signature` middleware wrapper around customer dashboard/laporan routes — customers now land directly on `customer.dashboard` after login without being forced to set a signature first
- `EnsureCustomerHasSignature` middleware + `customer.signature` alias kept in `Kernel.php` (unused); `/customer/profile/signature` page still available for voluntary use
- Staff (`web` guard) signature gate unchanged

---

## Offline Page Bug Fix (2026-06-09) ✅

- `resources/views/offline.blade.php` — fixed two CSS stacking bugs:
  - **`.pulse-ring`**: added missing `position: absolute` — without it, `inset: 0` had no effect and the three ring divs stacked as block elements in normal flow, pushing the icon down inside the 88px container and breaking layout
  - **`.icon-bg`**: added `position: relative; z-index: 1` — absolute-positioned elements paint above static elements in CSS stacking order, so the icon face was being hidden behind the pulse rings
  - Removed empty `.feat-text {}` CSS rule
