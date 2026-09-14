# Technical Requirements Document (TRD)

## Project
Recharge Mobile App (PWA)

## Version
1.0.0

---

## 1. Architecture Overview

### 1.1 MVC Lite Pattern

```
recharge-app/
├── app/
│   ├── controllers/    → Request handling
│   ├── models/         → Database queries
│   └── helpers/        → Utility functions
├── api/                → REST API endpoints
├── assets/
│   ├── css/            → Stylesheets
│   ├── js/             → JavaScript
│   ├── images/         → Images
│   └── icons/          → App icons
├── pages/              → Screen templates
├── database/           → SQL schemas
├── pwa/                → PWA files
├── docs/               → Documentation
└── index.php           → Entry point
```

### 1.2 Request Flow

```
Browser Request
    ↓
index.php (Router)
    ↓
Controller (Logic)
    ↓
Model (Database)
    ↓
JSON Response
```

## 2. Frontend Architecture

### 2.1 Single Page App (SPA) Simulation

- index.php loads as main entry
- Pages loaded dynamically via JavaScript
- Screen transitions with CSS animations
- State management via JavaScript object

### 2.2 Screen Loader

```javascript
// app/js/router.js
const Router = {
    screens: {
        'splash': 'pages/splash.php',
        'home': 'pages/home.php',
        'plans': 'pages/plans.php',
        // ...
    },
    navigate(screenId) {
        // Load screen content
        // Trigger transition
        // Initialize screen JS
    }
};
```

### 2.3 Component System

Each component has:
- HTML template (in page file)
- CSS file (in assets/css/)
- JS file (in assets/js/)

### 2.4 CSS Architecture

```
assets/css/
├── base.css           → Reset, variables, typography
├── components.css     → Shared component styles
├── splash.css         → S001 styles
├── home.css           → S002 styles
├── verify.css         → S003 styles
├── plans.css          → S004 styles
├── plan-detail.css    → S005 styles
├── checkout.css       → S006 styles
├── payment.css        → S007 styles
├── success.css        → S008 styles
├── failed.css         → S009 styles
└── history.css        → S010 styles
```

## 3. Backend Architecture

### 3.1 PHP 8.3 Requirements

- PDO for database
- JSON responses
- CORS headers
- Error handling
- Input validation

### 3.2 API Response Format

```json
{
    "status": "success|error",
    "message": "Description",
    "data": {}
}
```

### 3.3 Database Connection

```php
// app/helpers/database.php
class Database {
    private static $instance = null;
    
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new PDO(
                'mysql:host=localhost;dbname=recharge_app',
                'username',
                'password',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$instance;
    }
}
```

## 4. Database Schema

### 4.1 users

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 4.2 operators

```sql
CREATE TABLE operators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.3 plans

```sql
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operator_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    validity VARCHAR(50),
    description TEXT,
    data VARCHAR(50),
    calls VARCHAR(50),
    sms VARCHAR(50),
    category ENUM('popular', 'unlimited', 'data', 'talktime') DEFAULT 'popular',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operator_id) REFERENCES operators(id)
);
```

### 4.4 orders

```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    operator_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'success', 'failed') DEFAULT 'pending',
    reference_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    FOREIGN KEY (operator_id) REFERENCES operators(id)
);
```

### 4.5 payments

```sql
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    upi_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

### 4.6 transactions

```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_id INT,
    type ENUM('debit', 'credit', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (payment_id) REFERENCES payments(id)
);
```

### 4.7 settings

```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 4.8 logs

```sql
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('info', 'warning', 'error', 'debug') NOT NULL,
    message TEXT NOT NULL,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 5. API Implementation

### 5.1 verify-number.php

```php
// Input: { "mobile": "9876543210" }
// Output: { "status": "success", "data": { "operator": {...}, "circle": "..." } }
```

### 5.2 get-plans.php

```php
// Input: { "operator_id": 1 }
// Output: { "status": "success", "data": { "plans": [...] } }
```

### 5.3 create-order.php

```php
// Input: { "plan_id": 1, "mobile": "9876543210" }
// Output: { "status": "success", "data": { "order_id": 1, "upi_url": "..." } }
```

### 5.4 verify-payment.php

```php
// Input: { "order_id": 1, "transaction_id": "..." }
// Output: { "status": "success", "data": { "receipt": {...} } }
```

### 5.5 payment-webhook.php

```php
// Input: Gateway webhook data
// Output: { "status": "received" }
```

## 6. PWA Implementation

### 6.1 manifest.json

```json
{
    "name": "Recharge App",
    "short_name": "Recharge",
    "start_url": "/",
    "display": "fullscreen",
    "background_color": "#F7F5FF",
    "theme_color": "#6C2BFF",
    "icons": [...]
}
```

### 6.2 Service Worker

- Cache-first strategy for static assets
- Network-first for API calls
- Offline fallback page
- Background sync for failed payments

## 7. Performance Requirements

| Metric                | Target    |
|-----------------------|-----------|
| First Contentful Paint| < 1.5s    |
| Largest Contentful Paint| < 2.5s  |
| Time to Interactive   | < 3.0s    |
| Cumulative Layout Shift| < 0.1    |
| Total Bundle Size     | < 200KB   |

## 8. Security Requirements

- HTTPS enforced
- CSRF tokens on forms
- Input sanitization
- PDO prepared statements
- Session security
- Rate limiting on APIs
