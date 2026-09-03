# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

EMS ("Energy Management Solutions") — a vanilla PHP web app that reads electrical meters over Modbus, stores time-series readings in MySQL, and renders dashboards, gauges, phasor diagrams, reports, and PDF electricity bills. UI is bilingual Thai/English. No framework, no router, no build step.

## Running

There is no build or test tooling. The app runs under XAMPP (Apache + MySQL) from `C:\xampp\htdocs\ems`.

- **Serve:** start Apache + MySQL in XAMPP, open `http://localhost/ems/pages/login.php`. `pages/home.php` just redirects to login.
- **PHP alternative:** `php -S localhost:8000` from the repo root, then hit `/pages/login.php` (all includes are relative `../`, so the doc root must be the repo root).
- **PHP deps:** `composer install` (fpdf, tfpdf, endroid/qr-code, phpmailer, phpdotenv).
- **Database:** create a MySQL schema named by `.env`'s `DB_NAME` (currently `ams`) and import the tables. Key tables: `users`, `meter`, `meter_data`, `data_type`, plus location/group/room/notify tables. Rows are soft-deleted with `is_deleted = 0`; `meter.is_active` gates polling.
- **Meter poller:** `pip install pymodbus mysql-connector-python requests python-dotenv`, then `python connector/pymodbustcpAllmeters.py` (or run `connector/runscriptmeter.bat`). It loops every 60s, reads active meters, and POSTs each reading to `http://localhost/ems/config/meter-data.php`.
- **Debug:** `.vscode/launch.json` has Xdebug configs on port 9003.

`.env` (committed) holds MySQL config plus LINE messaging tokens. Note `.env.example` shows Postgres-style defaults, but the app connects with **mysqli** — `.env.example` is misleading.

## Architecture

### Per-feature file convention

Each screen `X` is spread across parallel files. Adding or changing a feature usually means editing all of these:

| File | Role |
|---|---|
| `pages/X.php` | HTML shell only. Includes `components/session.php`, sidemenu/header/footer, `styles/X.css`, `scripts/scriptjs.html` (shared), and `scripts/scriptjs-X.html` (page JS). |
| `scripts/scriptjs-X.html` | The page's JavaScript, wrapped in `<script>` tags despite the `.html` extension. Calls `config/*.php` endpoints via `fetch()`. |
| `styles/X.css` | Page styles. |
| `config/<verb>-<noun>.php` | AJAX endpoints returning JSON: `fetch-*`, `update-*`, `create-*`, `delete-*`. |

Files suffixed `-old` or `-v1` (e.g. `dashboard-old.php`, `scriptjs-diagram-v1.html`) are stale prior versions kept in-tree. **Edit only the unsuffixed file.**

### Request flow

Every `config/*.php` endpoint starts with `include "../config/no-crash.php"` (no-cache headers) and `include "../config/connect.php"`. `connect.php` requires `config.php` (which loads `.env` via phpdotenv into `$db_config`), opens the mysqli handle `$conn`, and calls `checktoken()`. Endpoints then run parameterized queries and `echo json_encode([...])`.

`connector/config.py` reads the same `.env` independently for the Python side.

### Session, i18n, theming — `components/session.php`

Included by every page. Responsibilities:
- `session_start()` + 1800s idle timeout.
- Auth guards: `checkLogin()` (redirects to login, then checks `checktoken()`), `check()`, `checkSession()`. **Many pages currently have these calls commented out.**
- The filter chain lives in the session: `$_SESSION['lid']` (location) → `['gid']` (group) → `['tid']` / `['tmid']` (meter type/meter). `checkSession()` enforces the chain by redirecting to the relevant management page. Helpers `setLocation()`, `setGroup()`, `setTypeMeter()`.
- i18n: `?lang=th|en` query param stored in `$_SESSION['lang']`; loads `lang/<code>.php` which defines the `$lang` associative array used as `<?= $lang['key'] ?>` throughout, and passed to JS as `const LANG`. **New UI strings must be added to both `lang/th.php` and `lang/en.php`.**
- Theming: `?theme=dark|light` stored in session; sets PHP color vars (`$bg`, `$bgsec`, `$text`, `$accentColor`, …) used inline in pages. Independently, client JS toggles `localStorage['theme']` and a `dark` class on `<html>`/`<body>`.

### Auth

`config/login.php` looks up `users` by username or email, verifies with `password_verify` against a `password_hash` column, and sets `$_SESSION['user_id']`, `username`, `is_admin`, `user`. `config/create-user.php` / `create-admin.php` hash with `PASSWORD_DEFAULT`.

### Data acquisition (`connector/`)

Python scripts poll meters and push readings back into the app via HTTP:
- `pymodbustcpAllmeters.py` — threaded Modbus TCP poller (main one; the `.bat` runs it).
- `pymodbus485Allmeters.py`, `pymodbusrs485.py`, `pymodbustcp.py` — RS485 / single-meter variants.
- `pynotifyDetect.py` — watches for disconnected meters and sends LINE alerts.
- `function.py` — `LINE_OA()` push-message helper (LINE Messaging API).
- `config/meter-data.php` is the ingest endpoint: maps each reading key to a `data_type` row and inserts one `meter_data` row per value.

### Billing / export

`config/create-pdf.php` + `config/fpdf.php` (`calculateBill()` — tiered Thai electricity tariff + service charge + 7% VAT) generate PDF bills into `downloads/<date>/` (gitignored). `config/sendToEmail.php` mails them via PHPMailer over Gmail SMTP.

### Frontend libraries

All via CDN, loaded in `scripts/ref.html` (in `<head>`) and `scripts/scriptjs.html` (before page JS): Bootstrap 5, jQuery 2, Chart.js 4 + date-fns adapter, CanvasJS, Raphael, JustGage / canvas-gauges / JSCharting, Babylon.js, SweetAlert2 (`alertMessages()` wrapper), SheetJS xlsx, jsPDF + autotable, qrcodejs, Syncfusion ej. `scripts/scriptjs.html` also defines shared helpers: `redirect()`, `showLoading()/hideLoading()`, `setSession()`, `formatDateTime()`, `fetchMeters()`, `fetchDataOfMeter()`, option generators.

## Things to know

- `components/serial.php` `checktoken()` is a USB hardware-dongle license check that is **entirely commented out and always returns `true`**. It is required by `connect.php` and `session.php`; leave the stub returning true unless working on licensing.
- `components/set-session.php` writes an arbitrary `$_SESSION[$_POST['key']]` with no auth — used by the client `setSession()` helper to persist filter selections.
- The git history uses many short-lived feature branches named `<feature>-js-N` and `N.Mf` / `N.Mf-fixed`; `master` is the PR base, current work is on `v1`.
