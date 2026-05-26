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
