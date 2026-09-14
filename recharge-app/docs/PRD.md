# Product Requirements Document (PRD)

## Project Name
Recharge Mobile App (PWA)

## Version
1.0.0

## Date
September 2026

---

## 1. Product Overview

A Mobile-First Progressive Web App (PWA) for mobile phone recharges. Built as a native-like experience for smartphone users. Premium glassmorphism design inspired by PhonePe recharge flow.

## 2. Target Users

- Smartphone-only users (Android + iOS)
- Age group: 18-45
- Tech-savvy mobile-first users
- Users who prefer UPI payments

## 3. Platform

- Mobile Web (PWA)
- Base Width: 390px
- Safe Area: iPhone notch support
- Min Width: 375px
- Max Width: 430px
- Desktop: NOT supported

## 4. Technology Stack

| Layer       | Technology           |
|-------------|----------------------|
| Frontend    | HTML5, CSS3, Vanilla JS (ES6) |
| Backend     | PHP 8.3, MySQL 8     |
| Architecture| MVC Lite, REST APIs  |
| PWA         | manifest.json, Service Worker |

## 5. User Flow

```
Splash (S001)
    ↓
Home - Enter Mobile Number (S002)
    ↓
Verify Loader (S003)
    ↓
Plans List (S004)
    ↓
Plan Detail (S005)
    ↓
Checkout (S006)
    ↓
Payment Verify (S007)
    ↓
Success (S008) / Failed (S009)
    ↓
Order History (S010)
```

## 6. Screens

| ID    | Screen Name      | Description                          |
|-------|------------------|--------------------------------------|
| S001  | Splash           | App splash with logo animation       |
| S002  | Home             | Enter mobile number, select operator |
| S003  | Verify Loader    | Number verification loading state    |
| S004  | Plans            | List of recharge plans               |
| S005  | Plan Detail      | Single plan details + selection      |
| S006  | Checkout         | Payment summary + UPI selection      |
| S007  | Payment Verify   | Payment processing state             |
| S008  | Success          | Payment success animation            |
| S009  | Failed           | Payment failure screen               |
| S010  | Order History    | Past transactions list               |

## 7. Components

| ID    | Component Name     | Used In           |
|-------|--------------------|-------------------|
| C001  | Header             | S002,S004,S010    |
| C002  | Offer Timer        | S002              |
| C003  | Operator Selector  | S002              |
| C004  | Mobile Input       | S002              |
| C005  | Verify Loader      | S003              |
| C006  | Plan Card          | S004              |
| C007  | Checkout Card      | S006              |
| C008  | Payment Modal      | S006              |
| C009  | Success Animation  | S008              |
| C010  | Bottom Safe Space  | All screens       |

## 8. API Endpoints

| Endpoint               | Method | Description              |
|------------------------|--------|--------------------------|
| verify-number.php      | POST   | Verify mobile number     |
| get-plans.php          | POST   | Get plans by operator    |
| create-order.php       | POST   | Create recharge order    |
| verify-payment.php     | POST   | Verify UPI payment       |
| payment-webhook.php    | POST   | Payment gateway webhook  |

## 9. Database Tables

| Table          | Description                    |
|----------------|--------------------------------|
| users          | Registered users               |
| operators      | Mobile operators               |
| plans          | Recharge plans                 |
| orders         | Recharge orders                |
| payments       | Payment records                |
| transactions   | Transaction logs               |
| settings       | App settings                   |
| logs           | System logs                    |

## 10. PWA Features

- Manifest with app icons
- Service Worker for offline support
- Add to Home Screen prompt
- Fullscreen mode
- Apple touch icons
- Offline splash screen

## 11. Design System

- Theme: Light only (No dark mode)
- Style: Premium Glassmorphism
- Primary: #6C2BFF
- Secondary: #8B5DFF
- Background: #F7F5FF
- Text: #141414
- Font: Poppins
- Border Radius: 24px
- Blur: 20px
- Animation: 300ms ease

## 12. Security

- Input validation on all fields
- SQL injection prevention (PDO)
- CSRF protection
- Session-based auth
- UPI payment verification server-side

## 13. Future Scope (Not in v1.0)

- Dark mode
- Desktop support
- Multiple payment methods
- Wallet integration
- Bill payments
- DTH recharge
