# UI System Documentation

## Project
Recharge Mobile App (PWA)

---

## 1. Design Tokens

### 1.1 Colors

```css
:root {
    /* Primary Colors */
    --color-primary: #6C2BFF;
    --color-primary-light: #8B5DFF;
    --color-primary-dark: #5A1FE0;
    
    /* Background */
    --color-bg: #F7F5FF;
    --color-bg-secondary: #EDE9FF;
    
    /* Text */
    --color-text: #141414;
    --color-text-secondary: #6B6B6B;
    --color-text-muted: #9E9E9E;
    
    /* Glass */
    --glass-bg: rgba(255, 255, 255, 0.72);
    --glass-border: rgba(255, 255, 255, 0.5);
    
    /* Status */
    --color-success: #22C55E;
    --color-error: #EF4444;
    --color-warning: #F59E0B;
    
    /* Shadows */
    --shadow-sm: 0 2px 8px rgba(108, 43, 255, 0.08);
    --shadow-md: 0 4px 16px rgba(108, 43, 255, 0.12);
    --shadow-lg: 0 8px 32px rgba(108, 43, 255, 0.16);
}
```

### 1.2 Typography

```css
:root {
    /* Font Family */
    --font-family: 'Poppins', sans-serif;
    
    /* Font Sizes */
    --text-xs: 12px;
    --text-sm: 14px;
    --text-base: 16px;
    --text-lg: 18px;
    --text-xl: 20px;
    --text-2xl: 24px;
    --text-3xl: 30px;
    --text-4xl: 36px;
    
    /* Font Weights */
    --weight-regular: 400;
    --weight-medium: 500;
    --weight-semibold: 600;
    --weight-bold: 700;
    
    /* Line Heights */
    --leading-tight: 1.2;
    --leading-normal: 1.5;
    --leading-relaxed: 1.75;
}
```

### 1.3 Spacing

```css
:root {
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
    --space-10: 40px;
}
```

### 1.4 Border Radius

```css
:root {
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;
}
```

### 1.5 Animation

```css
:root {
    --transition-fast: 150ms ease;
    --transition-base: 300ms ease;
    --transition-slow: 500ms ease;
}
```

---

## 2. Components

### 2.1 Glass Card

```css
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    padding: var(--space-5);
}
```

### 2.2 Primary Button

```css
.btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 56px;
    padding: var(--space-4) var(--space-6);
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
    color: white;
    font-family: var(--font-family);
    font-size: var(--text-base);
    font-weight: var(--weight-semibold);
    border: none;
    border-radius: var(--radius-xl);
    cursor: pointer;
    transition: all var(--transition-base);
    box-shadow: var(--shadow-md);
}

.btn-primary:active {
    transform: scale(0.98);
    box-shadow: var(--shadow-sm);
}
```

### 2.3 Secondary Button

```css
.btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 56px;
    padding: var(--space-4) var(--space-6);
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    color: var(--color-primary);
    font-family: var(--font-family);
    font-size: var(--text-base);
    font-weight: var(--weight-semibold);
    border: 1px solid var(--color-primary);
    border-radius: var(--radius-xl);
    cursor: pointer;
    transition: all var(--transition-base);
}

.btn-secondary:active {
    transform: scale(0.98);
}
```

### 2.4 Input Field

```css
.input-field {
    width: 100%;
    min-height: 56px;
    padding: var(--space-4) var(--space-5);
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    font-family: var(--font-family);
    font-size: var(--text-lg);
    color: var(--color-text);
    outline: none;
    transition: border-color var(--transition-base);
}

.input-field:focus {
    border-color: var(--color-primary);
}

.input-field::placeholder {
    color: var(--color-text-muted);
}
```

### 2.5 Header

```css
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-4) var(--space-5);
    padding-top: calc(var(--space-4) + env(safe-area-inset-top));
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--glass-border);
}

.header-title {
    font-size: var(--text-lg);
    font-weight: var(--weight-semibold);
    color: var(--color-text);
}
```

### 2.6 Plan Card

```css
.plan-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: var(--space-5);
    margin-bottom: var(--space-4);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
}

.plan-card:active {
    transform: scale(0.98);
    border-color: var(--color-primary);
}

.plan-card-amount {
    font-size: var(--text-2xl);
    font-weight: var(--weight-bold);
    color: var(--color-primary);
}

.plan-card-validity {
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
}

.plan-card-data {
    font-size: var(--text-base);
    font-weight: var(--weight-medium);
    color: var(--color-text);
}
```

### 2.7 Bottom Safe Space

```css
.bottom-safe {
    height: calc(80px + env(safe-area-inset-bottom));
}
```

---

## 3. Layout

### 3.1 Screen Container

```css
.screen {
    width: 100%;
    max-width: 430px;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    background: var(--color-bg);
    overflow-x: hidden;
    position: relative;
}

@media (min-width: 375px) and (max-width: 430px) {
    .screen {
        width: 100%;
    }
}
```

### 3.2 Content Container

```css
.content {
    padding: var(--space-5);
    padding-bottom: calc(80px + env(safe-area-inset-bottom));
}
```

### 3.3 Safe Areas (iPhone Notch)

```css
:root {
    --safe-top: env(safe-area-inset-top);
    --safe-bottom: env(safe-area-inset-bottom);
    --safe-left: env(safe-area-inset-left);
    --safe-right: env(safe-area-inset-right);
}

body {
    padding-top: var(--safe-top);
    padding-bottom: var(--safe-bottom);
    padding-left: var(--safe-left);
    padding-right: var(--safe-right);
}
```

---

## 4. Animations

### 4.1 Fade In

```css
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in {
    animation: fadeIn var(--transition-base) ease forwards;
}
```

### 4.2 Slide Up

```css
@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-up {
    animation: slideUp var(--transition-base) ease forwards;
}
```

### 4.3 Scale In

```css
@keyframes scaleIn {
    from { 
        opacity: 0;
        transform: scale(0.9);
    }
    to { 
        opacity: 1;
        transform: scale(1);
    }
}

.scale-in {
    animation: scaleIn var(--transition-base) ease forwards;
}
```

### 4.4 Pulse

```css
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.pulse {
    animation: pulse 2s ease-in-out infinite;
}
```

### 4.5 Success Checkmark

```css
@keyframes checkmark {
    0% { stroke-dashoffset: 100; }
    100% { stroke-dashoffset: 0; }
}

.success-checkmark {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: checkmark 0.5s ease forwards 0.3s;
}
```

---

## 5. Responsive Rules

### 5.1 Base

- Width: 390px
- Min: 375px
- Max: 430px

### 5.2 Breakpoints

```css
/* Small phones */
@media (max-width: 374px) {
    :root {
        --text-xl: 18px;
        --text-2xl: 22px;
        --text-3xl: 28px;
    }
}

/* Standard phones */
@media (min-width: 375px) and (max-width: 430px) {
    /* Default styles */
}

/* Larger phones */
@media (min-width: 431px) {
    .screen {
        box-shadow: var(--shadow-lg);
    }
}
```

---

## 6. Touch Targets

All interactive elements must have:
- Minimum width: 44px
- Minimum height: 44px
- Spacing between targets: 8px minimum

---

## 7. Icons

- Use SVG icons only
- Size: 24px default
- Color: inherit from parent
- No bitmap/raster icons

---

## 8. Images

- Format: SVG preferred, PNG fallback
- No stretching
- Object-fit: cover for backgrounds
- Object-fit: contain for icons
