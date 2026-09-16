# PROJECT AUDIT REPORT — QuickRecharge PWA

**Project:** QuickRecharge Mobile Recharge PWA
**Location:** `C:\Users\mohad\Downloads\m\htdocs\PWE\recharge-app`
**Audit Date:** September 14, 2026
**Auditor:** Automated Code Review

---

## SUMMARY SCORECARD

| Category | Rating | Notes |
|---|---|---|
| **Architecture** | 7/10 | Clean separation; no framework overhead; JSON storage limits scale |
| **Security** | 6/10 | Good admin hardening; hardcoded credentials; no CSRF on public API |
| **Code Quality** | 7/10 | Consistent style; good naming; inline JS in PHP pages |
| **Payment Security** | 8/10 | Frontend can never set SUCCESS; server-resolved amounts; HMAC webhook |
| **PWA Readiness** | 7/10 | SW with 3 cache strategies; manifest with icons; missing some icon files |
| **Scalability** | 4/10 | JSON file storage; no database; race conditions possible at scale |
| **Test Coverage** | 0/10 | No unit tests, integration tests, or CI/CD pipeline |
| **Production Readiness** | 5/10 | Functional MVP; hardcoded secrets; no automated backups |

**Overall Score: 5.5/10** — Solid prototype/MVP. Requires security hardening and database migration before production traffic.

---

## 1. EXECUTIVE SUMMARY

QuickRecharge is a production-grade Mobile Recharge Progressive Web Application (PWA) with a PhonePe-inspired UI. It provides a complete recharge flow: mobile number entry → operator detection → plan selection → UPI payment → success/failure.

**Key characteristics:**
- PHP 8+ backend with JSON file-based storage (no MySQL)
- Vanilla JavaScript SPA frontend with hash-based routing
- Manual UPI payment flow (collect via merchant VPA + dynamic QR/intent)
- Admin panel for managing plans, operators, payment settings, and verifying transactions
- PWA with offline support via Service Worker

**Critical finding:** The payment system is MANUAL_UPI only — there is no automated payment gateway. Server-side SUCCESS is granted only by webhook or admin verification. The frontend can NEVER self-certify a successful payment.

---

## 2. TECHNOLOGY STACK

| Layer | Technology | Details |
|---|---|---|
| Backend | PHP 8.0+ | No framework; single entry point (front controller) |
| Frontend | Vanilla JavaScript (ES6+) | No framework; SPA with client-side routing |
| Storage | JSON files | Via custom `DataStore` class (529 lines) |
| PWA | Service Worker | `pwa/sw.js` with 3 cache strategies |
| External Libraries | QRCode.js (CDN) | Loaded in `index.php` and `pages/checkout.php` |
| Fonts | Google Fonts (Poppins) | Preconnected and preloaded |
| Server | Apache with mod_rewrite | `.htaccess` for routing and security |

**Dependencies:**
- PHP 8.0+ (required)
- Apache with `mod_rewrite` (required)
- No `composer.json`, no `npm/package.json`
- No external PHP packages

---

## 3. FRONTEND ARCHITECTURE

### 3.1 SPA Routing

The app is a Single Page Application using a custom `Router` class (`assets/js/router.js:1-217`).

| Screen | Route | File | Purpose |
|---|---|---|---|
| Splash | `#splash` | `pages/splash.php` | Loading screen (skipped, redirects to home) |
| Home | `#home` | `pages/home.php` | Operator selection + mobile number input |
| Verify | `#verify` | `pages/verify.php` | Number verification + operator confirmation |
| Plans | `#plans` | `pages/plans.php` | Plan listing with filtering |
| Checkout | `#checkout` | `pages/checkout.php` | UPI payment method selection |
| Payment | `#payment` | `pages/payment.php` | Payment verification polling |
| Success | `#success` | `pages/success.php` | Recharge confirmation |
| Failed | `#failed` | `pages/failed.php` | Payment failure screen |

### 3.2 State Management

State is managed via `App.state` object (`assets/js/app.js:6-15`):

```javascript
state: {
    currentScreen: 'splash',
    user: null,
    selectedOperator: null,
    selectedPlan: null,
    currentOrder: null,
    mobileNumber: '',
    isLoading: false,
}
```

User data is persisted in `localStorage` under the key `recharge_user`.

### 3.3 Screen Loading Flow

1. Router fetches PHP file via `fetch()`
2. Extracts inline `<script>` tags via regex
3. Injects HTML into `#app` container
4. Dynamically executes extracted scripts
5. Calls `initScreenScripts()` to initialize screen-specific logic

### 3.4 CSS Architecture

| File | Purpose |
|---|---|
| `assets/css/base.css` | Shared styles, variables, base layout |
| `assets/css/home.css` | Home screen (carousel, operators, forms) |
| `assets/css/verify.css` | Number verification screen |
| `assets/css/plans.css` | Plan listing and filtering |
| `assets/css/checkout.css` | UPI payment selection + QR code |
| `assets/css/payment.css` | Payment verification progress |
| `assets/css/success.css` | Success confirmation |
| `assets/css/failed.css` | Error/failure screen |
| `assets/css/splash.css` | Splash/loading screen |

### 3.5 Mobile Features

The frontend implements several mobile-specific features (`assets/js/app.js:186-366`):
- **Swipe back navigation** — Left edge swipe returns to previous screen
- **Pinch zoom prevention** — Prevents unwanted zoom on input focus
- **Screen wake lock** — Keeps screen on during payment (`navigator.wakeLock`)
- **Orientation lock** — Forces portrait mode
- **Network status detection** — Online/offline toast notifications
- **Haptic feedback** — Vibration on key actions
- **Status bar color** — Dynamic theme color per screen

---

## 4. BACKEND ARCHITECTURE

### 4.1 Entry Point & Routing

**Single entry point:** `index.php` (322 lines) acts as a front controller.

Request routing:
1. `/api/*` requests → API endpoints in `api/` directory
2. Static files (`.css`, `.js`, `.svg`, `.png`, etc.) → Served directly with cache headers
3. `.php` files in `pages/` → Served via `readfile()` (not `require`)
4. All other requests → SPA shell (`index.php` HTML)

### 4.2 API Endpoints

| Endpoint | Method | File | Purpose |
|---|---|---|---|
| `/api/create-order` | POST | `api/create-order.php:1-132` | Create order + transaction |
| `/api/verify-number` | POST | `api/verify-number.php:1-109` | Detect operator from mobile prefix |
| `/api/verify-payment` | POST | `api/verify-payment.php:1-109` | Report/verify payment status |
| `/api/transaction-status` | GET | `api/transaction-status.php:1-43` | Poll transaction status |
| `/api/recharge-session` | GET/POST | `api/recharge-session.php:1-92` | Server-side session management |
| `/api/payment-webhook` | POST | `api/payment-webhook.php:1-184` | Gateway webhook receiver |
| `/api/get-plans` | GET/POST | `api/get-plans.php:1-46` | List active plans |
| `/api/home-content` | GET | `api/home-content.php:1-10` | Homepage content |
| `/api/payment-settings` | GET | `api/payment-settings.php:1-29` | UPI payment settings |

### 4.3 Data Layer

**DataStore class** (`admin/data/store.php:1-529`) — 529 lines, handles all CRUD operations.

| Method | Purpose |
|---|---|
| `getPlans()` / `getPlan()` / `savePlan()` / `deletePlan()` | Plan CRUD |
| `getOrders()` / `createOrder()` / `updateOrderStatus()` | Order management |
| `getTransactions()` / `createTransaction()` / `updateTransactionStatus()` | Transaction lifecycle |
| `getSettings()` / `saveSettings()` | App settings |
| `getOperators()` / `saveOperator()` / `deleteOperator()` | Operator management |
| `getCheckout()` / `saveCheckout()` | UPI payment config |
| `getHomeContent()` / `saveHomeContent()` | Homepage content |
| `expireStaleTransactions()` | Auto-expire old transactions |
| `getQrExpirySeconds()` | QR code expiry config |
| `isMethodEnabled()` | Payment method validation |
| `addLog()` / `getLogs()` | Payment logging |

**Storage mechanism:** `file_get_contents()` / `file_put_contents()` with `LOCK_EX` flag.

### 4.4 Payment Service

**PaymentService class** (`app/services/PaymentService.php:1-89`) — 89 lines, isolated payment logic.

Key methods:
- `normalizeMobile()` — Indian mobile number validation and normalization
- `validOperator()` — Operator whitelist check
- `validVpa()` — UPI VPA format validation
- `buildUpiUri()` — UPI payment URI construction
- `appDeepLink()` — Per-app deep links (PhonePe, GPay, Paytm)
- `verifyWithProvider()` — Placeholder for real gateway integration

---

## 5. DATABASE / DATA LAYER

### 5.1 JSON Files

| File | Purpose | Initial Size |
|---|---|---|
| `admin/data/plans.json` | 48 recharge plans (12 per operator) | 542 lines |
| `admin/data/orders.json` | Order records | Sample data |
| `admin/data/transactions.json` | Transaction records | Empty array |
| `admin/data/settings.json` | App settings (min/max recharge, UPI ID) | 6 fields |
| `admin/data/operators.json` | 4 operators (Jio, Airtel, Vi, BSNL) | 4 records |
| `admin/data/checkout.json` | UPI payment config | 14 fields |
| `admin/data/home.json` | Homepage content (banners, promos, stats) | Complex nested |
| `admin/data/payment_logs.json` | Payment event logs | Empty array |

### 5.2 Transaction ID Format

```
RCH{YYYYMMDD}{16-char-hex}
```
Example: `RCH20260914A1B2C3D4E5F6G7H8`

Generated via: `'RCH' . date('Ymd') . strtoupper(bin2hex(random_bytes(8)))`

### 5.3 Legacy MySQL Helper

**`app/helpers/database.php:1-83`** — PDO MySQL connection singleton. **NOT USED** by any active code. Exists as legacy from an earlier architecture.

---

## 6. ADMIN PANEL

### 6.1 Entry & Authentication

**Entry point:** `admin/index.php:1-247` (247 lines)

| Feature | Implementation |
|---|---|
| Auth method | `password_verify()` with bcrypt hash |
| Default credentials | `admin` / `admin123` (bcrypt hash at line 22) |
| Session security | `httponly`, `strict_mode`, `SameSite=Lax` |
| Session timeout | 30 minutes (1800 seconds) |
| CSRF protection | Tokens via `generateAdminCsrfToken()` / `verifyAdminCsrfToken()` |
| Rate limiting | 5 attempts / 15-minute lockout (file-based) |
| Audit logging | `admin/data/audit_log.json` (max 5000 entries) |
| Session regeneration | `session_regenerate_id(true)` on login |

### 6.2 Admin Pages

| Page | File | Purpose |
|---|---|---|
| Dashboard | `admin/pages/dashboard.php` | Stats overview (plans, orders, revenue) |
| Plans | `admin/pages/plans.php` | Plan listing with CRUD |
| Plan Form | `admin/pages/plan-form.php` | Add/edit plan |
| Operators | `admin/pages/operators.php` | Operator listing with CRUD |
| Operator Form | `admin/pages/operator-form.php` | Add/edit operator |
| Home Content | `admin/pages/home-content.php` | Homepage content management |
| Payment Settings | `admin/pages/payment-settings.php` | UPI app config, merchant VPA |
| Settings | `admin/pages/settings.php` | General app settings |
| Orders | `admin/pages/orders.php` | Transaction/order listing |
| Login | `admin/pages/login.php` | Admin login form |

### 6.3 Security Measures

- All POST operations require CSRF token validation (`admin/index.php:166-173`)
- DELETE operations require POST method + CSRF (`admin/index.php:184-207`)
- Admin/data directory blocked from direct web access (`.htaccess:24`)
- Session-based authentication with 30-minute timeout

---

## 7. CRM FEATURES

| Feature | Status |
|---|---|
| Customer accounts | **Not implemented** |
| User login | **Not implemented** |
| User data persistence | `localStorage` only (`recharge_user` key) |
| Mobile validation | Indian 10-digit, starts with 6-9 |
| Order history | Server-side only (admin panel) |
| User profiles | **Not implemented** |

The app is stateless from the user's perspective — no login, no history, no saved payment methods. User state is ephemeral and stored in `localStorage`.

---

## 8. PAYMENT / UPI SYSTEM

### 8.1 Payment Mode

**Mode: MANUAL_UPI** — Collect payments via merchant VPA + dynamic QR code or UPI app deep link.

There is **no automated payment gateway integration**. All payment confirmation requires:
1. A real gateway webhook (future), or
2. Explicit admin verification with UTR reference

### 8.2 Payment Flow

```
Home → Verify → Plans → Checkout → [UPI App / QR] → Payment → Success/Failed
```

1. **create-order** (`api/create-order.php:1-132`):
   - Validates mobile, plan, operator, UPI app
   - Server-resolves amount from plan database (never trusts client)
   - Creates order + INITIATED transaction
   - Returns UPI URI + app deep link

2. **UPI Payment** (`pages/checkout.php`):
   - User selects PhonePe / GPay / Paytm / QR
   - App redirect via deep link: `phonepe://`, `tez://`, `paytmmp://`
   - Fallback modal if app not installed

3. **Payment Verification** (`pages/payment.php`, `api/verify-payment.php`):
   - Frontend polls `transaction-status` every 3 seconds
   - Frontend can only report: PENDING, FAILED, CANCELLED
   - SUCCESS requires admin session or webhook
   - UTR reference submission for faster verification

### 8.3 UPI URI Construction

Built by `PaymentService::buildUpiUri()` (`app/services/PaymentService.php:46-56`):

```
upi://pay?pa={vpa}&pn={merchant}&am={amount}&cu=INR&tr={txnRef}&tn={note}
```

### 8.4 App Deep Links

| App | Deep Link Pattern |
|---|---|
| PhonePe | `phonepe://pay?{query}` |
| Google Pay | `tez://upi/pay?{query}` |
| Paytm | `paytmmp://pay?{query}` |
| Generic UPI | `upi://pay?{query}` |

---

## 9. QR CODE SYSTEM

| Feature | Implementation |
|---|---|
| Generation | Dynamic via QRCode.js library (CDN) |
| QR data | UPI URI with amount, merchant, transaction reference |
| Display | Inline in checkout screen (`pages/checkout.php:142-193`) |
| Expiry | Configurable 1-30 minutes (default 5 min) |
| Timer | Visual countdown with progress bar |
| Regeneration | Creates NEW backend transaction on expiry |
| Static QR | **Never used** — all QR is dynamic per transaction |

The QR code system is well-designed for security — each QR is tied to a specific transaction with a unique reference and expiry.

---

## 10. WEBHOOK SYSTEM

**Endpoint:** `api/payment-webhook.php:1-184`

| Feature | Implementation |
|---|---|
| Signature verification | HMAC-SHA256 (when `WEBHOOK_SECRET` env var is set) |
| Amount verification | Compares provider amount with server-stored amount |
| Currency verification | Must be INR |
| Idempotency | Duplicate SUCCESS is ignored |
| Logging | All webhooks logged to `payment_logs.json` |

**Security note:** When `WEBHOOK_SECRET` is not set, signature verification is skipped with a warning logged. This is appropriate for development but must be configured in production.

---

## 11. RECHARGE API AUDIT

### 11.1 create-order (`api/create-order.php`)

**Security measures:**
- Rate limited: 10 orders/minute/IP
- Server-resolves amount from plan DB (line 69-80) — client amount is IGNORED
- Validates mobile, plan, operator, UPI app against whitelists
- Validates UPI app is enabled in admin settings
- Validates VPA format before transaction creation
- Returns server-generated transaction ID and UPI URI

### 11.2 verify-payment (`api/verify-payment.php`)

**Security rules (critical):**
- Frontend can NEVER set SUCCESS (line 75-86)
- Frontend-allowed statuses: PENDING, FAILED, CANCELLED only
- SUCCESS requires admin session (`$_SESSION['admin_logged']`)
- Idempotent: terminal SUCCESS stays SUCCESS
- Rate limited: 20 verifications/minute/IP

**Bug found:** Duplicate `require_once` statements at lines 33-34:
```php
require_once $basePath . '/admin/data/store.php';
require_once $basePath . '/app/services/PaymentService.php';
```
These are already included at lines 23-24. Harmless due to `require_once` but indicates copy-paste error.

### 11.3 transaction-status (`api/transaction-status.php`)

- Auto-expires stale transactions (INITIATED/PENDING past `expires_at`)
- Returns server-truth status for frontend polling
- Never trusts client amount

### 11.4 recharge-session (`api/recharge-session.php`)

- Server-side session for authoritative recharge state
- Actions: `init`, `select-plan`, `reset`
- Frontend localStorage is NOT authoritative

---

## 12. PLAN / PRICING SYSTEM

### 12.1 Plan Distribution

| Operator | Plans | Price Range | ID Range |
|---|---|---|---|
| Jio | 12 | ₹199 - ₹999 | 1-12 |
| Airtel | 12 | ₹199 - ₹999 | 20-30 |
| Vi | 12 | ₹179 - ₹999 | 40-50 |
| BSNL | 12 | ₹187 - ₹999 | 60-70 |
| **Total** | **48** | **₹179 - ₹999** | |

### 12.2 Plan Categories

- **popular** — Entry-level plans (56-84 days)
- **unlimited** — Long-validity plans (180-730 days)
- **data** — Data-focused plans (unused in current seed data)
- **talktime** — Talktime plans (unused in current seed data)

### 12.3 Plan Fields

Each plan includes: `id`, `operator`, `amount`, `validity`, `data`, `calls`, `sms`, `category`, `badge`, `active`.

Admin can add/edit/delete plans via the admin panel. Plans are served from JSON, filtered by operator and active status. Client-side sorting, filtering, and search are implemented.

---

## 13. SECURITY AUDIT

### 13.1 XSS Protection

- `htmlspecialchars()` used on all PHP output (verified in `pages/home.php`, `pages/checkout.php`, etc.)
- `ENT_QUOTES` flag used in `Auth::sanitize()` helper
- User input validated/escaped before display

### 13.2 CSRF Protection

- Admin forms use CSRF tokens via `generateAdminCsrfToken()` / `verifyAdminCsrfToken()`
- Token validated on all POST operations (`admin/index.php:166-173`)
- Uses `hash_equals()` for timing-safe comparison

### 13.3 Rate Limiting

**File-based rate limiting** (`app/helpers/rate-limiter.php:1-87`):

| Endpoint | Limit | Window |
|---|---|---|
| create-order | 10 requests | 60 seconds |
| verify-number | 15 requests | 60 seconds |
| verify-payment | 20 requests | 60 seconds |

Rate limit state stored in temp directory as JSON files.

### 13.4 Session Security

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 when HTTPS enforced
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800);
```

### 13.5 Password Security

- `password_hash()` with `PASSWORD_DEFAULT` (bcrypt)
- `password_verify()` for comparison
- Default password: `admin123` (CHANGE IN PRODUCTION)

### 13.6 File Security (`.htaccess`)

| Rule | Purpose |
|---|---|
| `RewriteRule ^admin/data/ - [F,L]` | Block direct access to JSON data |
| `RewriteRule ^\.git - [F,L]` | Block git directory access |
| `RewriteRule ^\.env - [F,L]` | Block .env file access |
| `FilesMatch "^\. "` | Block hidden files |
| `Options -Indexes` | Disable directory listing |
| PHP execution blocked in `uploads/` | Prevent uploaded PHP execution |

### 13.7 CORS Configuration

```php
$allowedOrigins = [
    // Add your production domain(s) here
];
```

CORS is restrictive — empty allowed origins list. Same-origin requests work without CORS headers.

### 13.8 Security Headers

```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
```

### 13.9 Input Validation

- Mobile numbers: Regex validated (`/^[6-9]\d{9}$/`)
- Operators: Whitelist check against `['jio', 'airtel', 'vi', 'bsnl']`
- UPI apps: Whitelist check against `['phonepe', 'gpay', 'paytm', 'qr']`
- Plan IDs: Integer cast and database lookup
- Admin page/action: Regex sanitized (`/[^a-z\-]/`)

### 13.10 Vulnerabilities Found

| Severity | Issue | Location |
|---|---|---|
| **CRITICAL** | Hardcoded admin password (`admin123`) | `admin/index.php:22` |
| **HIGH** | No `.env` file — credentials in source code | `admin/index.php:19-24` |
| **HIGH** | HTTPS not enforced (commented out in `.htaccess`) | `.htaccess:30-31` |
| **MEDIUM** | `session.cookie_secure = 0` (should be 1 in production) | `admin/index.php:10` |
| **MEDIUM** | No CSRF tokens on public API endpoints | `api/*.php` |
| **MEDIUM** | No automated backups of JSON data | Architecture |
| **LOW** | QRCode.js loaded twice (index.php + checkout.php) | `index.php:277`, `pages/checkout.php:222` |
| **LOW** | Service worker references missing icon files | `pwa/sw.js:13-30` |
| **LOW** | Legacy `database.php` included in API routing | `index.php:39` |
| **LOW** | Duplicate `require_once` in verify-payment.php | `api/verify-payment.php:33-34` |

---

## 14. ENVIRONMENT / SECRETS

| Secret | Location | Status |
|---|---|---|
| Admin username | `admin/index.php:21` — `ADMIN_USER` | Hardcoded: `admin` |
| Admin password hash | `admin/index.php:22` — `ADMIN_PASS_HASH` | Hardcoded bcrypt of `admin123` |
| WEBHOOK_SECRET | `getenv('WEBHOOK_SECRET')` | Environment variable (optional) |
| UPI ID | `admin/data/checkout.json` | Configurable via admin panel |
| MySQL credentials | `app/helpers/database.php:12-14` | Hardcoded: `root` / empty password (unused) |

**No `.env` file exists.** All credentials are hardcoded in PHP source files.

---

## 15. DEPENDENCIES

| Dependency | Type | Source | Used By |
|---|---|---|---|
| PHP 8.0+ | Runtime | Server | All PHP files |
| Apache + mod_rewrite | Server | Server | `.htaccess` routing |
| QRCode.js 1.0.0 | JS Library | CDN (`cdnjs.cloudflare.com`) | `index.php:277`, `pages/checkout.php:222` |
| Google Fonts (Poppins) | Font | CDN (`fonts.googleapis.com`) | `index.php:142-145` |

**No package manager files** (`composer.json`, `package.json`, `yarn.lock`).

---

## 16. ROUTING

### 16.1 Front Controller

`index.php` handles all requests through a front controller pattern:

1. **API routes** — `/api/{endpoint}` → `api/{endpoint}.php`
2. **Static assets** — CSS, JS, images served directly with `Cache-Control: max-age=86400`
3. **PHP pages** — Served via `readfile()` (not `require`) for security
4. **SPA shell** — All other routes serve the SPA HTML

### 16.2 API Route Map

```php
$apiRoutes = [
    'verify-number'      => 'api/verify-number.php',
    'get-plans'          => 'api/get-plans.php',
    'create-order'       => 'api/create-order.php',
    'verify-payment'     => 'api/verify-payment.php',
    'transaction-status' => 'api/transaction-status.php',
    'recharge-session'   => 'api/recharge-session.php',
    'payment-webhook'    => 'api/payment-webhook.php',
    'home-content'       => 'api/home-content.php',
    'payment-settings'   => 'api/payment-settings.php',
];
```

---

## 17. PWA FEATURES

### 17.1 Service Worker (`pwa/sw.js:1-209`)

| Strategy | Cache Name | Applies To |
|---|---|---|
| Cache-first | `recharge-static-v3` | CSS, JS, images, manifest |
| Cache-first | `recharge-dynamic-v3` | PHP pages |
| Network-first | `recharge-api-v3` | API requests (`/api/*`) |

### 17.2 Web App Manifest (`pwa/manifest.json`)

- Name: "QuickRecharge — Fast • Secure • Instant"
- Display: standalone
- Orientation: portrait
- Theme color: #5F259F (purple)
- Icons: 8 sizes (72, 96, 128, 144, 152, 192, 384, 512)

### 17.3 PWA Installation

- Install banner shown after 5 seconds
- Deferred prompt pattern (`beforeinstallprompt`)
- Dismissible with `localStorage` persistence
- `appinstalled` event handler

### 17.4 Offline Support

- Static assets: Cache-first (available offline)
- API requests: Network-first with cache fallback
- Navigation: Falls back to cached SPA shell

### 17.5 Push Notifications

Placeholder implementation in `sw.js:150-182`. Not yet connected to a push service.

### 17.6 Background Sync

`sync-recharge` tag handler for pending recharges (`sw.js:184-209`). Retrieves pending orders from cache and submits via `create-order` API.

---

## 18. UNUSED FILES / CODE

| File | Status | Notes |
|---|---|---|
| `app/helpers/database.php` | **UNUSED** | Legacy MySQL PDO helper; no active code references it except `index.php:39` include |
| `app/helpers/auth.php` | **PARTIALLY USED** | Only `validateMobile()` and `formatMobile()` used by `verify-number.php`; rest unused |
| `app/helpers/response.php` | **PARTIALLY USED** | Only used by `verify-number.php`; other API endpoints use raw `json_encode()` |
| `app/helpers/rate-limiter.php` | **ACTIVE** | Used by `create-order`, `verify-number`, `verify-payment` |

---

## 19. PROTECTED COMPONENTS / DATA

| Component | Protection |
|---|---|
| `admin/data/store.php` | Core class; requires PHP include (not directly accessible) |
| `admin/data/*.json` | Blocked by `.htaccess` (`RewriteRule ^admin/data/ - [F,L]`) |
| `admin/index.php` | Session-based auth required for all pages except login |
| `app/services/PaymentService.php` | Service class; not directly accessible |
| `.htaccess` | Apache config; not served as text |
| `uploads/` | PHP execution blocked (`FilesMatch "\.php$" → Require all denied`) |

---

## 20. VERCEL COMPATIBILITY

**NOT COMPATIBLE with Vercel.**

| Requirement | Status |
|---|---|
| Apache + mod_rewrite | Vercel uses serverless functions |
| PHP runtime | Vercel does not natively support PHP |
| JSON file storage | Ephemeral filesystem on Vercel |
| Session-based auth | Serverless sessions require external store |

**Migration to Vercel would require:**
1. Convert PHP to Node.js/Python serverless functions
2. Replace JSON storage with database (PlanetScale, Supabase, etc.)
3. Replace session auth with JWT or external session store
4. Rebuild admin panel in a supported framework

---

## 21. DEPLOYMENT REQUIREMENTS

| Requirement | Details |
|---|---|
| PHP version | 8.0 or higher |
| Web server | Apache with `mod_rewrite` enabled |
| Writable directories | `uploads/`, `admin/data/` |
| HTTPS | Strongly recommended (HSTS header present) |
| Memory | Minimal (no heavy processing) |
| Disk | ~10MB for app + growing JSON data files |

---

## 22. RISKS & ISSUES

### 22.1 Security Risks

| Risk | Severity | Mitigation |
|---|---|---|
| Hardcoded admin credentials | **Critical** | Move to `.env` file; change default password |
| No HTTPS enforcement | **High** | Uncomment `.htaccess` HTTPS redirect; set `cookie_secure=1` |
| No automated backups | **High** | Implement scheduled JSON backup to cloud storage |
| Race conditions on concurrent writes | **Medium** | `LOCK_EX` mitigates but doesn't eliminate; migrate to DB |

### 22.2 Code Issues

| Issue | Location | Impact |
|---|---|---|
| QRCode.js loaded twice | `index.php:277`, `checkout.php:222` | Double initialization; wasted bandwidth |
| SW references missing icon files | `pwa/sw.js:13-30` | `icon-72.png`, `icon-96.png`, etc. referenced but not in `assets/icons/` |
| Legacy `database.php` included | `index.php:39` | Unnecessary include; may error if MySQL extension missing |
| Duplicate `require_once` | `api/verify-payment.php:33-34` | Harmless but indicates copy-paste error |
| `session.cookie_secure = 0` | `admin/index.php:10` | Session cookie sent over HTTP |

### 22.3 Operational Risks

| Risk | Impact |
|---|---|
| No database | JSON files don't scale; data loss risk |
| No logging framework | `error_log()` only; no structured logging |
| No monitoring | No health checks or uptime monitoring |
| No CI/CD | Manual deployment only |
| No test suite | No automated regression testing |

---

## 23. RECOMMENDED ARCHITECTURE (FUTURE)

### Phase 1 — Security Hardening (Immediate)
1. Move all secrets to `.env` file
2. Change default admin password
3. Enable HTTPS enforcement
4. Set `session.cookie_secure = 1`
5. Remove legacy `database.php` include
6. Fix duplicate `require_once` in verify-payment.php
7. Deduplicate QRCode.js loading

### Phase 2 — Database Migration (Short-term)
1. Migrate to MySQL/PostgreSQL
2. Convert DataStore class to use PDO
3. Add proper schema with indexes
4. Implement connection pooling

### Phase 3 — Payment Gateway (Medium-term)
1. Integrate Razorpay, Cashfree, or PhonePe gateway
2. Replace manual UPI flow with automated verification
3. Add webhook signature verification
4. Implement automated reconciliation

### Phase 4 — Production Scale (Long-term)
1. Add user authentication and accounts
2. Implement proper API versioning
3. Add comprehensive error handling and logging
4. Create CI/CD pipeline
5. Add unit and integration tests
6. Implement monitoring and alerting

---

## 24. FILE INVENTORY

### 24.1 File Count Summary

| Type | Count | Notes |
|---|---|---|
| PHP files | 37 | Backend + admin + pages |
| CSS files | 9 | Base + per-screen styles |
| JS files | 2 | `app.js` (369 lines), `router.js` (217 lines) |
| JSON data files | 8 | In `admin/data/` |
| JSON config | 1 | `pwa/manifest.json` |
| JavaScript (SW) | 1 | `pwa/sw.js` (209 lines) |
| SVG icons | 4 | Operator logos (`assets/icons/`) |
| Image files | 9 | Uploaded images (`uploads/`) |
| Other | 1 | `.htaccess` (43 lines) |
| **Total** | **72** | |

### 24.2 Key File Line Counts

| File | Lines | Purpose |
|---|---|---|
| `admin/data/store.php` | 529 | Core data layer |
| `pages/home.php` | 694 | Homepage (PHP + inline JS) |
| `pages/checkout.php` | 514 | Checkout with QR code |
| `pages/payment.php` | 281 | Payment verification |
| `admin/index.php` | 247 | Admin entry + auth |
| `assets/js/app.js` | 369 | Frontend state manager |
| `assets/js/router.js` | 217 | SPA router |
| `index.php` | 322 | Front controller + SPA shell |
| `pwa/sw.js` | 209 | Service Worker |
| `api/payment-webhook.php` | 184 | Webhook handler |
| `api/create-order.php` | 132 | Order creation |

---

## 25. CODE QUALITY NOTES

### 25.1 Strengths

- **Consistent coding style** across all PHP files
- **Good separation of concerns** — DataStore, PaymentService, API endpoints, frontend screens
- **Security-conscious payment flow** — frontend can never self-certify SUCCESS
- **Well-structured admin panel** with CSRF, rate limiting, audit logging
- **Comprehensive PWA** with offline support and installation flow
- **Clean mobile UX** — swipe back, haptic feedback, wake lock, orientation lock

### 25.2 Weaknesses

- **Inline JS in PHP pages** — Not ideal for caching/maintenance but functional
- **No unit tests or integration tests** — Zero test coverage
- **No CI/CD configuration** — Manual deployment only
- **No build pipeline** — No minification, bundling, or asset optimization
- **JSON file storage** — Doesn't scale; race condition risk
- **Hardcoded credentials** — Security risk
- **No input sanitization on admin forms** beyond basic validation
- **No structured logging** — Only `error_log()` calls

### 25.3 Recommendations Priority

| Priority | Action |
|---|---|
| P0 | Change default admin password; move secrets to `.env` |
| P0 | Enable HTTPS enforcement |
| P1 | Fix duplicate require_once; remove legacy database.php include |
| P1 | Deduplicate QRCode.js loading |
| P2 | Add automated JSON backups |
| P2 | Implement proper input validation on admin forms |
| P3 | Add unit tests for PaymentService and DataStore |
| P3 | Set up CI/CD pipeline |

---

*End of Audit Report*
