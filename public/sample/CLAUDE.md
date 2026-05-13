# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**SIPRT** (Sistem Informasi Penugasan & Pelaporan Teknisi) is a static HTML/CSS/JS UI mockup of a mobile-first PWA for technician task assignment and reporting. There is no build system, no dependencies to install, and no backend — open `index.html` directly in a browser.

## Known bug

`index.html` references `style.css` but the actual file is named `styke.css`. Either rename the file to `style.css` or fix the `<link>` tag.

## Architecture

All six app screens live as `<section class="page" id="page-*">` elements inside a single `index.html`. Only one section is visible at a time via the `.active` CSS class. The phone-frame wrapper gives the desktop view its mobile device appearance.

**Navigation** (`app.js` → `goTo(pageId)`):
- Sidebar nav buttons (`data-page` attribute)
- `onclick="goTo('...')"` inline handlers on back/forward buttons
- Keyboard: `←` / `→` arrow keys or number keys `1`–`6`
- Touch swipe left/right on the phone frame

**Page order and IDs:**

| # | `id` suffix | Screen |
|---|---|---|
| 1 | `dashboard-sales` | Sales dashboard with task list and technician status |
| 2 | `form-tugas` | Multi-step task creation form |
| 3 | `dashboard-teknisi` | Technician's own task view |
| 4 | `form-laporan` | Technician report form (checklist, photos, materials, signature) |
| 5 | `template-builder` | Drag-and-drop custom report template editor |
| 6 | `laporan-customer` | Read-only customer-facing report with rating |

**CSS design tokens** are in `:root` inside `styke.css` — `--blue`, `--green`, `--yellow`, `--orange`, `--red`, and semantic tokens like `--bg`, `--card-bg`, `--border`, `--text`, `--text-secondary`, `--radius-*`.

**Interactive behaviors wired in `app.js`:**
- `showToast(msg)` — fixed bottom toast notification
- Filter tabs, priority selectors, tab bars, toggle switches — mutual-exclusion click handlers
- `.tech-select-card` — single-select with checkmark fill
- `.builder-field` — click-to-activate with left accent bar; drag-and-drop reorder within `.builder-canvas`
- `.sign-canvas.empty` — click to "sign" (renders an SVG stroke and updates status)
- `.form-input:not(.disabled)` / `.form-textarea` — `contenteditable` for mockup field editing
- Status strip "Ubah" button — cycles through 4 technician statuses
- Star rating — hover preview + click to set rating

## Responsive behavior

The sidebar collapses off-screen on viewports `≤ 680px` and slides in via `.sidebar.open` toggled by the hamburger menu. The phone frame becomes full-width with no border radius.
