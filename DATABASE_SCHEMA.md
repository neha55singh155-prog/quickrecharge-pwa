# Database Schema — Recharge App

## Architecture Overview

This project uses **JSON file-based storage** via the `DataStore` class (`admin/data/store.php`). There is **no MySQL database** — all persistent data lives in 8 JSON files under `admin/data/`.

```
admin/data/
├── store.php            # DataStore class — the single point of access for all data
├── operators.json       # Telecom operator definitions
├── plans.json           # Recharge plan catalog
├── orders.json          # Customer order records
├── transactions.json    # Payment transaction ledger (primary)
├── settings.json        # Global application settings
├── home.json            # Homepage content / CMS
├── checkout.json        # Checkout page & UPI payment config
└── payment_logs.json    # API/payment event log
```

**Auxiliary files** (not managed by DataStore):

| File | Location | Max Entries | Purpose |
|---|---|---|---|
| `audit_log.json` | `admin/data/` | 5,000 | Admin action audit trail (created by `admin/index.php`) |
| `rate_limit_*.json` | system temp dir | — | Login rate-limiting state (`admin_login_attempts.json`) |

All writes use `LOCK_EX` (exclusive file lock) to prevent data loss from concurrent writes. The `DataStore` class is the **sole reader/writer** — no file should be edited directly while the app is running.

---

## 1. `operators.json`

**Type:** Array of objects
**Default count:** 4 operators (Jio, Airtel, Vi, BSNL)
**Managed by:** `DataStore::getOperators()`, `saveOperator()`, `deleteOperator()`, `updateOperatorOrder()`

### Schema

| Field | Type | Description |
|---|---|---|
| `id` | int | Unique identifier. Auto-incremented on create (`max_id + 1`). |
| `name` | string | Display name (e.g. `"Jio"`, `"Airtel"`) |
| `code` | string | Machine identifier. One of: `jio`, `airtel`, `vi`, `bsnl` |
| `color` | string | CSS gradient string for UI theming (e.g. `"linear-gradient(135deg,#0A3D91,#1E6DD1)"`) |
| `image` | string | Path to operator logo image, or `""` for none |
| `active` | bool | Whether the operator is shown in the app |
| `order` | int | Display sort order. `getOperators()` sorts by this ascending. |

### Example

```json
{
  "id": 1,
  "name": "Jio",
  "code": "jio",
  "color": "linear-gradient(135deg,#0A3D91,#1E6DD1)",
  "image": "",
  "active": true,
  "order": 1
}
```

### Notes

- The `code` field is the **foreign key** used in `plans.json`, `orders.json`, and `transactions.json` to reference an operator.
- `order` is used for display sorting. Drag-and-drop reordering calls `updateOperatorOrder()` which accepts `[id => order]` mappings.
- Deleting an operator does **not** cascade-delete its plans.

---

## 2. `plans.json`

**Type:** Array of objects
**Default count:** 48 plans (12 per operator)
**Managed by:** `DataStore::getPlans()`, `getPlan()`, `savePlan()`, `deletePlan()`

### Schema

| Field | Type | Description |
|---|---|---|
| `id` | int | Unique identifier. Auto-incremented on create. IDs are chunked by operator (1–12 Jio, 20–30 Airtel, 40–50 Vi, 60–70 BSNL). |
| `operator` | string | Operator code. References `operators.json` → `code`. One of: `jio`, `airtel`, `vi`, `bsnl` |
| `amount` | float | Plan price in INR (e.g. `199`, `299.50`) |
| `validity` | string | Plan validity description (e.g. `"56 Days"`, `"84 Days"`, `"2.5 Years"`) |
| `data` | string | Data allowance (e.g. `"1.5GB/day"`, `"2GB/day"`) |
| `calls` | string | Calling benefit (typically `"Unlimited"`) |
| `sms` | string | SMS allowance (e.g. `"100/day"`, `"150/day"`) |
| `category` | string | Plan category. One of: `popular`, `unlimited`, `data`, `talktime` |
| `badge` | string | UI badge label. One of: `NEW`, `HOT`, `POPULAR`, `BEST`, `SPECIAL`, `PREMIUM`, `PRO`, `VIP`, `ELITE`, or `""` (no badge) |
| `active` | bool | Whether the plan is shown to users |

### Example

```json
{
  "id": 2,
  "operator": "jio",
  "amount": 249,
  "validity": "84 Days",
  "data": "2GB/day",
  "calls": "Unlimited",
  "sms": "100/day",
  "category": "popular",
  "badge": "NEW",
  "active": true
}
```

### Notes

- `operator` is a **string code** (not an ID) — it matches `operators.json` → `code`.
- Plans are filtered by operator on the frontend using `getPlans($operator)`.
- Plan IDs are **not sequential across operators** — they use chunked ranges to avoid collisions when new operators are added.

---

## 3. `orders.json`

**Type:** Array of objects
**Default count:** 5 demo orders
**Managed by:** `DataStore::getOrders()`, `createOrder()`, `updateOrderStatus()`

### Schema

| Field | Type | Description |
|---|---|---|
| `id` | int | Auto-incremented order ID (starts at 1001 in demo data) |
| `mobile` | string | Customer mobile number (10 digits) |
| `operator` | string | Operator code. References `operators.json` → `code`. |
| `amount` | float | Order amount in INR |
| `plan` | string | Human-readable plan summary (e.g. `"2GB/day - 84 Days"`) |
| `status` | string | Order status. One of: `pending`, `success`, `failed` |
| `created` | string | ISO datetime of order creation (`Y-m-d H:i:s`) |

### Example

```json
{
  "id": 1001,
  "mobile": "9876543210",
  "operator": "jio",
  "amount": 299,
  "plan": "2GB/day - 84 Days",
  "status": "success",
  "created": "2026-09-12 14:30:00"
}
```

### Notes

- `id` auto-increments from the highest existing ID in the array.
- `status` is a simplified view — the **canonical payment status** lives in `transactions.json`.
- The `plan` field is a denormalized string snapshot of the plan details at order time.

---

## 4. `transactions.json`

**Type:** Array of objects
**Default:** Empty array `[]`
**Managed by:** `DataStore::createTransaction()`, `getTransactions()`, `getTransaction()`, `updateTransactionStatus()`, `setTransactionUtr()`, `expireStaleTransactions()`

This is the **primary payment ledger** — the authoritative record of all payment attempts.

### Schema

| Field | Type | Description |
|---|---|---|
| `transaction_id` | string | Unique ID. Format: `RCH` + `date('Ymd')` + `strtoupper(bin2hex(random_bytes(8)))` (e.g. `RCH20260914A1B2C3D4E5F6G7H8`) |
| `reference_id` | string | External reference ID (e.g. order ID link) |
| `mobile` | string | Customer mobile number |
| `operator` | string | Operator code. References `operators.json` → `code`. |
| `plan_id` | int | Plan ID. References `plans.json` → `id`. |
| `plan_amount` | float | Plan price at time of transaction |
| `amount` | float | Transaction amount (equals `plan_amount`) |
| `upi_id` | string | Customer's UPI ID |
| `merchant_upi_id` | string | Merchant UPI ID receiving payment |
| `payment_method` | string | UPI app used (e.g. `"phonepe"`, `"gpay"`, `"paytm"`, `"qr"`) |
| `upi_reference` | string | UPI reference/UTR (sanitized: alphanumeric only, max 32 chars) |
| `gateway_reference` | string | Payment gateway reference |
| `created_at` | string | ISO datetime of creation (`Y-m-d H:i:s`) |
| `expires_at` | string | ISO datetime when transaction expires (`Y-m-d H:i:s`) |
| `status` | string | Transaction status. One of: `INITIATED`, `PENDING`, `SUCCESS`, `FAILED`, `EXPIRED`, `CANCELLED` |
| `updated_at` | string | ISO datetime of last status change. **Optional** — only present after first update. |

### Example

```json
{
  "transaction_id": "RCH20260914A1B2C3D4E5F6G7H8",
  "reference_id": "1001",
  "mobile": "9876543210",
  "operator": "jio",
  "plan_id": 3,
  "plan_amount": 299,
  "amount": 299,
  "upi_id": "user@paytm",
  "merchant_upi_id": "merchant@upi",
  "payment_method": "phonepe",
  "upi_reference": "927100123456",
  "gateway_reference": "",
  "created_at": "2026-09-14 10:30:00",
  "expires_at": "2026-09-14 10:35:00",
  "status": "PENDING",
  "updated_at": "2026-09-14 10:31:22"
}
```

### Transaction Lifecycle

```
INITIATED → PENDING → SUCCESS
            ↓
          FAILED / CANCELLED
            ↓
          EXPIRED (auto, if past expires_at)
```

- `INITIATED`: Transaction created, awaiting UPI confirmation.
- `PENDING`: UPI reference received via `setTransactionUtr()`, waiting for verification.
- `SUCCESS`: Payment confirmed and verified.
- `FAILED`: Payment failed at gateway or verification.
- `EXPIRED`: Auto-set by `expireStaleTransactions()` when `expires_at` is past and status is still `INITIATED` or `PENDING`.
- `CANCELLED`: Manually cancelled or user-abandoned.

### Notes

- **Expiry window** is configurable via `checkout.json` → `qr_expiry_minutes` (1–30 minutes, default 5).
- `expireStaleTransactions()` is called on each API request to auto-expire stale transactions.
- `setTransactionUtr()` sanitizes UTR to alphanumeric-only (`preg_replace('/[^A-Za-z0-9]/', '')`) and truncates to 32 chars.
- Transaction IDs are **crypto-secure** using `random_bytes(8)` — 16 hex characters + date prefix.

---

## 5. `settings.json`

**Type:** Object (not an array)
**Managed by:** `DataStore::getSettings()`, `saveSettings()`

### Schema

| Field | Type | Default | Description |
|---|---|---|---|
| `app_name` | string | `"Recharge App"` | Application display name |
| `maintenance_mode` | string | `"0"` | `"0"` = off, `"1"` = on. Stored as string, not boolean. |
| `min_recharge` | string | `"10"` | Minimum recharge amount in INR |
| `max_recharge` | string | `"10000"` | Maximum recharge amount in INR |
| `support_email` | string | `"support@rechargeapp.com"` | Support contact email |
| `UPI_ID` | string | `"recharge@upi"` | Merchant UPI ID (note: uppercase `UPI_ID` key) |

### Example

```json
{
  "app_name": "Recharge App",
  "maintenance_mode": "0",
  "min_recharge": "10",
  "max_recharge": "10000",
  "support_email": "support@rechargeapp.com",
  "UPI_ID": "recharge@upi"
}
```

### Notes

- All numeric values (`min_recharge`, `max_recharge`) are stored as **strings**, not numbers.
- `maintenance_mode` is also a string (`"0"` / `"1"`), not a boolean.
- `UPI_ID` uses uppercase `UPI` — this is inconsistent with other snake_case keys but is the established convention.

---

## 6. `home.json`

**Type:** Object
**Managed by:** `DataStore::getHomeContent()`, `saveHomeContent()`, `getTopBannerSlides()`, `saveTopBannerSlides()`

### Top-Level Schema

| Field | Type | Description |
|---|---|---|
| `top_banner_slides` | array | Carousel banner slides (see sub-schema below) |
| `app_name` | string | App branding name displayed on homepage |
| `app_tagline` | string | Tagline text (e.g. `"Fast • Secure • Instant"`) |
| `offer_title` | string | Countdown offer section title |
| `offer_minutes` | int | Countdown timer initial minutes |
| `offer_seconds` | int | Countdown timer initial seconds |
| `promo_brand` | string | Featured brand name in promo section |
| `promo_title` | string | Promo section heading |
| `promo_subtitle` | string | Promo section subheading |
| `promo_hot_deal` | string | Hot deal badge text |
| `promo_plans` | array | Featured plan cards (see sub-schema below) |
| `stats` | array | Stats bar items (see sub-schema below) |
| `trust_items` | array | Trust badge strings (see sub-schema below) |

### `top_banner_slides[]` Sub-Schema

| Field | Type | Description |
|---|---|---|
| `id` | int | Slide identifier |
| `title` | string | Slide headline |
| `subtitle` | string | Slide description |
| `bg_color` | string | Background hex color (e.g. `"#5F259F"`) |
| `accent_color` | string | Accent text/icon color |
| `icon` | string | Emoji or icon character |
| `active` | bool | Whether slide is shown |
| `order` | int | Display sort order |

### `promo_plans[]` Sub-Schema

| Field | Type | Description |
|---|---|---|
| `price` | int | Plan price in INR |
| `data` | string | Data allowance (e.g. `"2GB/DAY"`) |
| `validity` | string | Validity text (e.g. `"84 DAYS"`) |
| `calls` | string | Calling benefit |
| `featured` | bool | Whether plan is highlighted |
| `ribbon` | string | Ribbon label (e.g. `"MOST PICKED"`, `"BEST VALUE"`, `"PREMIUM"`, or `""`) |

### `stats[]` Sub-Schema

| Field | Type | Description |
|---|---|---|
| `value` | string | Stat value (e.g. `"4.8★"`, `"2M+"`) |
| `label` | string | Stat label (e.g. `"App rating"`, `"Recharges"`) |

### `trust_items`

Simple array of strings: `["Protected", "Instant Plans", "Verified"]`

### Example (top-level)

```json
{
  "top_banner_slides": [
    { "id": 1, "title": "Cashback Offer", "subtitle": "Get 10% cashback on first recharge", "bg_color": "#5F259F", "accent_color": "#FFD54F", "icon": "💰", "active": true, "order": 1 }
  ],
  "app_name": "QuickRecharge",
  "app_tagline": "Fast • Secure • Instant",
  "offer_title": "Special Offer Ends In",
  "offer_minutes": 9,
  "offer_seconds": 48,
  "promo_brand": "PhonePe",
  "promo_title": "BEST SAVINGS OFFERS",
  "promo_subtitle": "Save more on every recharge • Limited period",
  "promo_hot_deal": "HOT DEAL — 2M+ Recharges",
  "promo_plans": [
    { "price": 399, "data": "2GB/DAY", "validity": "180 DAYS", "calls": "Unlimited", "featured": true, "ribbon": "MOST PICKED" }
  ],
  "stats": [
    { "value": "4.8★", "label": "App rating" }
  ],
  "trust_items": ["Protected", "Instant Plans", "Verified"]
}
```

### Notes

- `promo_plans` are **static CMS content** — they do not reference `plans.json` by ID. They are a curated editorial selection.
- `top_banner_slides` are sorted by `order` ascending on render.

---

## 7. `checkout.json`

**Type:** Object
**Managed by:** `DataStore::getCheckout()`, `saveCheckout()`, `getQrExpirySeconds()`, `getActivePaymentMethods()`

### Schema

| Field | Type | Description |
|---|---|---|
| `hero_title` | string | Checkout page hero heading |
| `hero_sub` | string | Checkout page hero subtitle |
| `upi_id` | string | Merchant UPI ID for payments |
| `merchant_name` | string | Merchant name for UPI apps |
| `merchant_display_name` | string | Display name shown on checkout |
| `currency` | string | Currency code (e.g. `"INR"`) |
| `description` | string | Payment description |
| `qr_image` | string | Path to QR code image, or `""` for generated QR |
| `upi_apps` | array | Available UPI payment methods (see sub-schema below) |
| `security_text` | string | Security assurance heading |
| `security_sub` | string | Security assurance description |
| `trust_items` | array | Trust badge strings |
| `summary_title` | string | Order summary section heading |
| `summary_sub` | string | Order summary section subheading |
| `qr_expiry_minutes` | int | QR code expiry window in minutes (1–30, default 5) |

### `upi_apps[]` Sub-Schema

| Field | Type | Description |
|---|---|---|
| `id` | string | Payment method identifier. One of: `phonepe`, `gpay`, `paytm`, `qr` |
| `name` | string | Display name (e.g. `"PhonePe"`, `"Google Pay"`) |
| `desc` | string | Short description |
| `badge` | string | Badge label (e.g. `"Popular"`, or `""`) |
| `color` | string | Brand hex color (e.g. `"#5F259F"`) |
| `logo` | string | Path to logo image, or `""` |
| `active` | bool | Whether this payment method is available |

### Example

```json
{
  "hero_title": "Secure Checkout",
  "hero_sub": "UPI • Verified • Fast",
  "upi_id": "merchant@upi",
  "merchant_name": "QuickRecharge",
  "merchant_display_name": "QuickRecharge Store",
  "currency": "INR",
  "description": "Mobile Recharge",
  "qr_image": "",
  "upi_apps": [
    { "id": "phonepe", "name": "PhonePe", "desc": "Recommended • Fastest", "badge": "Popular", "color": "#5F259F", "logo": "", "active": true }
  ],
  "security_text": "100% Secure Payments",
  "security_sub": "Your payment is protected with UPI and bank-level security",
  "trust_items": ["Secure", "Instant Recharge", "Trusted by Millions"],
  "summary_title": "Recharge Summary",
  "summary_sub": "Please check your details before payment",
  "qr_expiry_minutes": 5
}
```

### Notes

- `qr_expiry_minutes` is clamped to 1–30 in `getQrExpirySeconds()` and defaults to 5.
- `upi_apps` are filtered by `active` flag and validated against the allowlist `['phonepe', 'gpay', 'paytm', 'qr']` in `getActivePaymentMethods()`.
- If `checkout.json` is empty or missing, `getCheckout()` re-runs `init()` to restore defaults.

---

## 8. `payment_logs.json`

**Type:** Array of objects
**Default:** Empty array `[]`
**Managed by:** `DataStore::addLog()`, `getLogs()`

### Schema

| Field | Type | Description |
|---|---|---|
| `at` | string | ISO datetime of log entry (`Y-m-d H:i:s`) |
| `event` | string | Event name (e.g. `"payment.initiated"`, `"payment.success"`, `"qr.generated"`) |
| `context` | object | Arbitrary key-value context data for the event |

### Example

```json
{
  "at": "2026-09-14 10:30:00",
  "event": "payment.initiated",
  "context": {
    "transaction_id": "RCH20260914A1B2C3D4E5F6G7H8",
    "amount": 299,
    "mobile": "9876543210"
  }
}
```

### Notes

- **Maximum 2,000 entries.** Older entries are trimmed automatically via `array_slice($logs, -2000)`.
- This is an append-only log — entries are never modified, only added.
- `getLogs($limit)` returns the most recent entries (default 100) in reverse chronological order.

---

## 9. `audit_log.json` (Auxiliary)

**Type:** Array of objects
**Location:** `admin/data/audit_log.json`
**Max entries:** 5,000
**Managed by:** `auditLog()` function in `admin/index.php` (not part of `DataStore`)

### Schema

| Field | Type | Description |
|---|---|---|
| `at` | string | ISO datetime (`Y-m-d H:i:s`) |
| `event` | string | Audit event name (e.g. `admin.login.success`, `admin.plan.delete`) |
| `ip` | string | Client IP address (`$_SERVER['REMOTE_ADDR']`) |
| `context` | object | Event-specific data (e.g. `["user" => "admin"]`, `["id" => 5]`) |

### Recorded Events

| Event | When |
|---|---|
| `admin.login.success` | Successful admin login |
| `admin.login.failed` | Failed admin login attempt |
| `admin.logout` | Admin logout |
| `admin.plan.delete` | Plan deleted |
| `admin.operator.delete` | Operator deleted |

### Notes

- Maximum 5,000 entries, auto-trimmed on write.
- **Does NOT use `LOCK_EX`** — unlike the DataStore files, this function uses plain `file_put_contents()`. This is a minor concurrency risk if two admin sessions write simultaneously.
- Written independently from `DataStore` to avoid coupling admin session logic with the data layer.

---

## Relationships Between Files

```
operators.json ◄─── plans.json          (plans.operator → operators.code)
       ▲                    ▲
       │                    │
       │              transactions.json  (transactions.operator → operators.code)
       │              transactions.json  (transactions.plan_id → plans.id)
       │                    │
       ▼                    ▼
  orders.json ◄──── transactions.json   (transactions.reference_id → orders.id)
                      (orders.operator → operators.code)

checkout.json ──► transactions.json      (upi_id, merchant_upi_id, qr_expiry_minutes)
settings.json ──► (global app config)
home.json     ──► (CMS — no foreign keys)
payment_logs.json ──► (append-only event log)
```

### Key Relationships

| Source | Field | Target | Field | Type |
|---|---|---|---|---|
| `plans.json` | `operator` | `operators.json` | `code` | Many-to-one (soft ref) |
| `orders.json` | `operator` | `operators.json` | `code` | Many-to-one (soft ref) |
| `transactions.json` | `operator` | `operators.json` | `code` | Many-to-one (soft ref) |
| `transactions.json` | `plan_id` | `plans.json` | `id` | Many-to-one (soft ref) |
| `transactions.json` | `reference_id` | `orders.json` | `id` | One-to-one (soft ref) |
| `transactions.json` | `merchant_upi_id` | `checkout.json` | `upi_id` | Configuration |

**All relationships are soft references** — there are no foreign key constraints, cascading deletes, or referential integrity enforcement at the storage level. Deleting an operator will leave orphaned plans, orders, and transactions referencing its code.

---

## Data Integrity & Concurrency

### File Locking

All writes through `DataStore::writeJSON()` use PHP's `LOCK_EX` flag:

```php
file_put_contents($file, $json, LOCK_EX);
```

This acquires an exclusive lock during write, preventing interleaved writes from concurrent HTTP requests from corrupting data. The read-then-write pattern is still **not atomic** — a race condition can occur if two requests read the same state before either writes. For a single-server, low-traffic deployment this is acceptable.

### ID Generation

| Entity | Strategy | Example |
|---|---|---|
| Plans | `max(id) + 1` across entire array | Last ID is 70 → next is 71 |
| Orders | `max(id) + 1` across entire array | Last ID is 1005 → next is 1006 |
| Operators | `max(id) + 1` across entire array | Last ID is 4 → next is 5 |
| Transactions | Crypto-secure: `RCH` + date + `bin2hex(random_bytes(8))` | `RCH20260914A1B2C3D4E5F6G7H8` |

Transaction IDs are **globally unique and unpredictable** — safe for external-facing references.

### Expiry & Auto-Cleanup

| Mechanism | File | Trigger | Behavior |
|---|---|---|---|
| Transaction expiry | `transactions.json` | `expireStaleTransactions()` on each API request | Sets status to `EXPIRED` for `INITIATED`/`PENDING` transactions past `expires_at` |
| Payment log trimming | `payment_logs.json` | On each `addLog()` call | Trims to last 2,000 entries |
| Audit log trimming | `audit_log.json` | On each `auditLog()` call | Trims to last 5,000 entries |
| QR expiry window | `checkout.json` | `getQrExpirySeconds()` | Clamps `qr_expiry_minutes` to 1–30 |

### Idempotency

- **Transaction creation** (`createTransaction`) is **not idempotent** — calling it twice creates two separate transactions with different IDs.
- **Plan/Operator upsert** (`savePlan`, `saveOperator`) checks for existing `id` and merges via `array_merge()` — updating an existing record with the same ID is idempotent.
- **Order creation** (`createOrder`) is **not idempotent** — always appends a new record.

### Failure Modes

| Scenario | Result |
|---|---|
| Two requests create transactions simultaneously | Both succeed — `LOCK_EX` serializes writes, but both read the same pre-write state |
| Two requests create orders simultaneously | Possible duplicate IDs (both read `max_id` = N, both write N+1) |
| `transactions.json` exceeds disk quota | `file_put_contents()` returns `false`, write silently fails |
| JSON file corrupted (partial write) | `json_decode()` returns `null`, `readJSON()` returns `[]`, data effectively lost |
| App reads after `LOCK_EX` fails | Returns empty/stale data gracefully (never crashes) |

### Backup Recommendation

Since there is no database dump mechanism, backing up this application requires copying the `admin/data/` directory. The `LOCK_EX` flag ensures a copy taken mid-write will either see the old or new state — never a partial JSON file.
