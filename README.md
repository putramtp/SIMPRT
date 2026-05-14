# SIPRT — Sistem Informasi Penugasan dan Pelaporan Teknisi

A web-based task assignment and technician work-reporting system built with Laravel 10. Sales staff assign jobs to technicians, technicians fill digital work reports (with photo and signature), and customers can view their service history through a secure shared link — no account required.

---

## Features

- **Role-based access control** — `admin`, `sales`, and `teknisi` roles with granular permissions
- **Task management** — sales creates and delegates tasks to technicians; status tracked (pending → in progress → completed)
- **Digital work reports** — technicians submit reports with description, photo upload, and dual signature canvas (technician + customer representative)
- **PDF export** — download any work report as a formatted A4 PDF
- **Customer report portal** — share a 30-day signed link with customers for read-only report access (no login needed)
- **Custom report templates** — sales builds reusable field templates via a drag-and-drop builder
- **Real-time notifications** — Pusher-powered toast alerts when a new task is assigned to a technician
- **PWA** — installable on mobile, offline fallback page, background sync (queues offline report submissions and retries when back online)
- **REST API** — Sanctum token auth for mobile app integration (tasks + reports endpoints)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 10, PHP 8.1+ |
| Database | MySQL |
| Auth | Laravel UI (session) + Laravel Sanctum (API tokens) |
| RBAC | Spatie Laravel Permission v6 |
| Frontend | Bootstrap 5, jQuery 3.7.1, DataTables 1.13.8 — all CDN, no build step |
| Icons | Tabler Icons + Bootstrap Icons (CDN) |
| PDF | barryvdh/laravel-dompdf |
| Real-time | Pusher + Laravel Echo |
| PWA | Web App Manifest + Service Worker |

---

## Requirements

- PHP 8.1+
- Composer
- MySQL 8.0+
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

---

## Installation

```bash
# 1. Clone
git clone <repo-url> siprt
cd siprt

# 2. Install PHP dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME=SIPRT
APP_URL=http://localhost

DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Database
php artisan migrate
php artisan db:seed

# 5. Storage link (for uploaded photos)
php artisan storage:link

# 6. Serve
php artisan serve
```

Open `http://localhost:8000`.

---

## Default Accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@siprt.com` | `password` |
| Sales | `sales1@siprt.com` | `password` |
| Teknisi | `teknisi1@siprt.com` | `password` |

The seeder also creates 10 sample customers, 20 tasks, and associated reports.

---

## User Roles

| Role | Capabilities |
|---|---|
| **Admin** | Everything — manage users, assign roles, full task/report/customer/template control |
| **Sales** | Create and manage tasks, manage customers, build templates, view all reports |
| **Teknisi** | View assigned tasks, submit work reports |
| **Customer** | Read-only access via shared signed link (no account required) |

---

## Key Pages

| URL | Description |
|---|---|
| `/dashboard/sales` | KPI overview, task table, technician status panel |
| `/dashboard/teknisi` | Assigned tasks with inline detail panel |
| `/tugas` | Task list (DataTable) |
| `/tugas/create` | New task form with live summary preview |
| `/laporan` | Report list (DataTable) |
| `/laporan/create` | Report form — drag-drop photo, dual signature canvas, offline sync |
| `/laporan/{id}` | Report detail — magazine layout, PDF download, print |
| `/laporan/{id}/pdf` | Direct PDF download |
| `/template` | Template builder — palette / canvas / property panel |
| `/customers/{id}/laporan` | Customer report history + shareable signed link |
| `/c/{customer}/laporan` | Public customer report view (signed URL, no auth) |

---

## API

All API endpoints are prefixed `/api`. Token auth via `Authorization: Bearer <token>`.

```
POST   /api/auth/login          Get access token
POST   /api/auth/logout         Revoke token
GET    /api/auth/me             Authenticated user info

GET    /api/tasks               List tasks (teknisi sees only assigned)
GET    /api/tasks/{id}          Task detail

GET    /api/reports             List reports (teknisi sees only own)
GET    /api/reports/{id}        Report detail
POST   /api/reports             Submit report (teknisi only, supports base64 photo)
```

### Login example

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"teknisi1@siprt.com","password":"password"}'
```

Response:
```json
{
  "token": "1|abc...",
  "user": { "id": 3, "name": "Teknisi 1", "email": "...", "roles": ["teknisi"] }
}
```

---

## PWA & Offline

The app registers a service worker on login. When offline:
- Previously visited pages are served from cache
- The `/offline` fallback page is shown for uncached navigation
- Report submissions are queued in IndexedDB and automatically replayed when connectivity is restored (`laporan-sync` background sync)

To install as a mobile app, tap **Add to Home Screen** in the browser menu.

---

## Real-time Notifications (Pusher)

Optional. Without configuration the app works normally — notifications are silently skipped.

To enable, add to `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap1
```

Then run `php artisan queue:work` for async broadcasting.

---

## Testing

```bash
php artisan test
```

24 feature tests covering:
- Task CRUD authorization (`TugasTest`)
- Report ownership enforcement (`LaporanTest`)
- Signed URL validation (`PublicLaporanTest`)
- API authentication and token flow (`Api/AuthTest`)

---

## Project Structure

```
app/
  Http/Controllers/
    Api/              ← Sanctum API controllers (Auth, Task, Report)
    DashboardController.php
    TugasController.php
    LaporanController.php
    CustomerController.php
    TemplateController.php
    UserController.php
  Events/TaskAssigned.php
  Models/             ← User, Customer, Task, Report, Template, Technician

resources/views/
  layouts/app.blade.php       ← Main authenticated layout
  layouts/public.blade.php    ← Public (signed URL) layout
  dashboard/                  ← sales.blade.php, teknisi.blade.php
  tugas/                      ← index, create, edit, show
  laporan/                    ← index, create, edit, show, customer, pdf
  template/index.blade.php
  offline.blade.php

public/
  css/public.css    ← Design system, layout components, mobile-first
  css/public.js     ← BP utility, flash dismiss
  sw.js             ← Service worker
  favicon/          ← Icons + site.webmanifest

tests/Feature/      ← TugasTest, LaporanTest, PublicLaporanTest, Api/AuthTest
```

---

## License

Private project — all rights reserved.
