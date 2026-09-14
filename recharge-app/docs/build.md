# Build Instructions (Single Source of Truth)

## Project
Recharge Mobile App (PWA)

## Status
COMPLETED

---

## Build Order

### Phase 1: Foundation (DO FIRST)

| Step | Task                          | Files                            | Status      |
|------|-------------------------------|----------------------------------|-------------|
| 1.1  | Create base CSS               | assets/css/base.css              | COMPLETE    |
| 2.1  | Create database schema        | database/schema.sql              | COMPLETE    |
| 3.1  | Create index.php router       | index.php                        | COMPLETE    |
| 4.1  | Create database helper        | app/helpers/database.php         | COMPLETE    |
| 5.1  | Create API response helper    | app/helpers/response.php         | COMPLETE    |
| 6.1  | Create PWA manifest           | pwa/manifest.json                | COMPLETE    |
| 7.1  | Create service worker         | pwa/sw.js                        | COMPLETE    |
| 8.1  | Create app router JS          | assets/js/router.js              | COMPLETE    |
| 9.1  | Create app state JS           | assets/js/app.js                 | COMPLETE    |

### Phase 2: Screens (BUILD ONE AT A TIME)

| Step | Screen     | Files                                    | Status      |
|------|------------|------------------------------------------|-------------|
| 2.1  | S001 Splash| pages/splash.php, assets/css/splash.css | COMPLETE    |
| 2.2  | S002 Home  | pages/home.php, assets/css/home.css     | COMPLETE    |
| 2.3  | S003 Verify| pages/verify.php, assets/css/verify.css | COMPLETE    |
| 2.4  | S004 Plans | pages/plans.php, assets/css/plans.css   | COMPLETE    |
| 2.5  | S005 Detail| pages/plan-detail.php, assets/css/plan-detail.css | COMPLETE |
| 2.6  | S006 Checkout| pages/checkout.php, assets/css/checkout.css | COMPLETE |
| 2.7  | S007 Payment| pages/payment.php, assets/css/payment.css | COMPLETE  |
| 2.8  | S008 Success| pages/success.php, assets/css/success.css | COMPLETE  |
| 2.9  | S009 Failed | pages/failed.php, assets/css/failed.css  | COMPLETE   |
| 2.10 | S010 History| pages/history.php, assets/css/history.css | COMPLETE  |

### Phase 3: Backend APIs

| Step | API Endpoint          | Files                    | Status  |
|------|-----------------------|--------------------------|---------|
| 3.1  | verify-number.php     | api/verify-number.php    | COMPLETE |
| 3.2  | get-plans.php         | api/get-plans.php        | COMPLETE |
| 3.3  | create-order.php      | api/create-order.php     | COMPLETE |
| 3.4  | verify-payment.php    | api/verify-payment.php   | COMPLETE |
| 3.5  | payment-webhook.php   | api/payment-webhook.php  | COMPLETE |

### Phase 4: MVC Models

| Step | Model         | Files                    | Status  |
|------|---------------|--------------------------|---------|
| 4.1  | User Model    | app/models/User.php      | COMPLETE |
| 4.2  | Operator Model| app/models/Operator.php  | COMPLETE |
| 4.3  | Plan Model    | app/models/Plan.php      | COMPLETE |
| 4.4  | Order Model   | app/models/Order.php     | COMPLETE |
| 4.5  | Payment Model | app/models/Payment.php   | COMPLETE |

### Phase 5: MVC Controllers

| Step | Controller         | Files                         | Status  |
|------|--------------------|-------------------------------|---------|
| 5.1  | Auth Controller    | app/controllers/AuthController.php | COMPLETE |
| 5.2  | Plan Controller    | app/controllers/PlanController.php | COMPLETE |
| 5.3  | Order Controller   | app/controllers/OrderController.php | COMPLETE |
| 5.4  | Payment Controller | app/controllers/PaymentController.php | COMPLETE |

---

## Screen Build Checklist

For EACH screen, complete these steps:

1. Create page PHP file (pages/)
2. Create CSS file (assets/css/)
3. Create JS file if needed (assets/js/)
4. Add component styles
5. Test on 375px width
6. Test on 430px width
7. Check safe areas
8. Check animations
9. Verify no overflow
10. Mark as COMPLETE

---

## Verification Loop

After building each screen:

Round 1: Compare with reference
Round 2: Fix spacing
Round 3: Fix typography
Round 4: Fix alignment
Round 5: Fix colors
Round 6: Final check

MAX 6 rounds. Then move to next screen.

---

## Quality Checklist

Before completing any screen:

- [ ] Correct spacing (4,8,12,16,20,24,32,40)
- [ ] Correct font size
- [ ] Correct radius (24px default)
- [ ] Correct blur (20px glass)
- [ ] Correct shadow (soft floating)
- [ ] Responsive 375-430
- [ ] Safe area working
- [ ] No overflow
- [ ] No horizontal scroll
- [ ] 44px tap targets
- [ ] 56px button height
- [ ] Glass card working
- [ ] Animation smooth (300ms ease)

---

## Design Tokens (COPY EXACTLY)

```
Primary:      #6C2BFF
Secondary:    #8B5DFF
Background:   #F7F5FF
Text:         #141414
Glass:        rgba(255,255,255,.72)
Radius:       24px
Blur:         20px
Font:         Poppins
Animation:    300ms ease
Base Width:   390px
Min Width:    375px
Max Width:    430px
Button Min:   56px
Tap Target:   44px
```

---

## Rules

- NEVER create random folders
- NEVER rename files
- NEVER redesign entire screens
- ONLY edit requested component
- ALWAYS reply with: Files Edited + Changes
- ALWAYS preserve project consistency
