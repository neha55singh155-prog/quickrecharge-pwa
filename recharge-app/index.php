<?php
// Production configuration
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', sys_get_temp_dir() . '/recharge_app_errors.log');

// --- Security Headers ---
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// --- CORS: restrict to same origin for API ---
$allowedOrigins = [
    'https://dsa.fatimber.com',
    'http://dsa.fatimber.com',
];
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($requestOrigin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
    header('Access-Control-Allow-Credentials: true');
}
// For same-origin requests, no CORS header needed (browsers allow it)
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

if (strpos($uri, '/api/') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/app/helpers/database.php';
    require_once __DIR__ . '/app/helpers/response.php';
    require_once __DIR__ . '/app/helpers/auth.php';
    $endpoint = substr($uri, 5);
    // Normalize: strip query leftovers, leading slash and optional .php suffix
    $endpoint = preg_replace('/\.php$/', '', ltrim($endpoint, '/'));
    $endpoint = trim($endpoint, '/');
    $apiRoutes = [
        'verify-number' => 'api/verify-number.php',
        'get-plans' => 'api/get-plans.php',
        'create-order' => 'api/create-order.php',
        'verify-payment' => 'api/verify-payment.php',
        'transaction-status' => 'api/transaction-status.php',
        'recharge-session' => 'api/recharge-session.php',
        'payment-webhook' => 'api/payment-webhook.php',
        'home-content' => 'api/home-content.php',
        'payment-settings' => 'api/payment-settings.php',
    ];
    if (isset($apiRoutes[$endpoint])) {
        require_once __DIR__ . '/' . $apiRoutes[$endpoint];
        exit;
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
    exit;
}

if (preg_match('/\.(css|js|svg|png|jpg|jpeg|gif|webp|ico|json)$/', $uri)) {
    $filePath = __DIR__ . $uri;
    if (file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css', 'js' => 'application/javascript',
            'svg' => 'image/svg+xml', 'png' => 'image/png',
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif', 'webp' => 'image/webp',
            'ico' => 'image/x-icon', 'json' => 'application/json',
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            header('Cache-Control: public, max-age=86400');
            readfile($filePath);
            exit;
        }
    }
    http_response_code(404);
    exit;
}

if (preg_match('/\.php$/', $uri)) {
    $filePath = __DIR__ . $uri;
    if (file_exists($filePath)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($filePath);
        exit;
    }
    http_response_code(404);
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="https://dsa.fatimber.com/">
    <meta charset="UTF-8">

    <!-- Mobile Viewport — no zoom, safe areas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

    <!-- Theme -->
    <meta name="theme-color" content="#5F259F">
    <meta name="color-scheme" content="light">

    <!-- iOS PWA -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="QuickRecharge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- iOS Splash Screens -->
    <link rel="apple-touch-icon" href="assets/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/icons/icon-152.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/icons/icon-128.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/icons/icon-72.png">

    <!-- SEO -->
    <meta name="description" content="India's fastest mobile recharge app. Pay with UPI via PhonePe, GPay, Paytm. Instant recharge, 100% secure.">
    <meta name="keywords" content="mobile recharge, UPI, PhonePe, Google Pay, Paytm, prepaid recharge, fast recharge">
    <meta name="author" content="QuickRecharge">

    <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="QuickRecharge">
    <meta name="msapplication-TileColor" content="#5F259F">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- PWA Manifest -->
    <link rel="manifest" href="pwa/manifest.json">

    <!-- Fonts — preload for speed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/base.css">

    <title>QuickRecharge — Fast • Secure • Instant</title>

    <style>
        /* ===== MOBILE APP SHELL ===== */
        *{ -webkit-touch-callout:none; -webkit-user-select:none; user-select:none; }
        input, textarea{ -webkit-user-select:text; user-select:text; }

        html{
            height:100%;
            overflow: hidden;
            overscroll-behavior: none;
        }

        body{
            background:#F5F0FF;
            height:100%;
            overscroll-behavior: none;
            margin:0;
        }

        /* The app container scrolls */
        #app{
            height:100%;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
        }

        /* Safe areas for notch devices */
        @supports(padding: max(0px)) {
            body { padding-top: max(0px, env(safe-area-inset-top)); }
        }

        /* Boot loader */
        #app:empty{
            min-height:100vh; min-height:100dvh;
            display:flex; flex-direction:column;
            align-items:center; justify-content:center; gap:20px;
            animation: bootFadeIn 0.4s ease;
        }
        #app:empty::before{
            content:''; width:72px; height:72px; border-radius:20px;
            background: linear-gradient(135deg,#5F259F 0%,#7B3FA0 50%,#9B59B6 100%);
            box-shadow:0 8px 32px rgba(95,37,159,0.35), 0 0 60px rgba(95,37,159,0.15);
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7l-5.5 2.9 1-6.2L3 8.6l6.2-.9L12 2Z'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:center; background-size:36px;
            animation: bootPulse 1.5s ease-in-out infinite;
        }
        #app:empty::after{
            content:'Loading QuickRecharge...';
            font-family:'Poppins',sans-serif; font-size:13px; font-weight:600;
            color:#6B6B6B; letter-spacing:0.3px;
        }
        #app:empty .boot-dots{ display:flex; gap:6px; margin-top:-4px; }
        #app:empty .boot-dots span{
            width:6px; height:6px; border-radius:50%; background:#5F259F;
            opacity:0.2; animation: bootDot 1.2s infinite;
        }
        #app:empty .boot-dots span:nth-child(2){ animation-delay:0.15s; }
        #app:empty .boot-dots span:nth-child(3){ animation-delay:0.3s; }
        @keyframes bootFadeIn{ from{ opacity:0; transform:scale(0.95); } to{ opacity:1; transform:scale(1); } }
        @keyframes bootPulse{ 0%,100%{ transform:scale(1); } 50%{ transform:scale(1.05); } }
        @keyframes bootDot{ 0%,80%,100%{ opacity:0.2; transform:scale(0.7); } 40%{ opacity:1; transform:scale(1.2); } }

        /* Install banner */
        #install-banner{
            position:fixed; bottom:0; left:0; right:0; z-index:9999;
            background:linear-gradient(135deg,#5F259F,#7B3FA0);
            padding:16px 20px; padding-bottom:calc(16px + env(safe-area-inset-bottom));
            display:none; align-items:center; gap:12px;
            box-shadow:0 -4px 24px rgba(95,37,159,0.3);
            animation: slideUpIn 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
        }
        #install-banner.show{ display:flex; }
        .ib-icon{ width:44px; height:44px; border-radius:12px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .ib-icon svg{ width:24px; height:24px; }
        .ib-text{ flex:1; }
        .ib-title{ font-size:14px; font-weight:700; color:white; }
        .ib-sub{ font-size:12px; color:rgba(255,255,255,0.8); }
        .ib-btn{ padding:10px 20px; background:white; color:#5F259F; font-family:'Poppins',sans-serif; font-size:13px; font-weight:700; border:none; border-radius:12px; cursor:pointer; flex-shrink:0; }
        .ib-close{ width:32px; height:32px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.15); border:none; border-radius:50%; cursor:pointer; flex-shrink:0; }
        .ib-close svg{ width:16px; height:16px; color:white; }
        @keyframes slideUpIn{ from{ transform:translateY(100%); } to{ transform:translateY(0); } }

        /* Swipe back indicator */
        #swipe-back{
            position:fixed; left:0; top:0; bottom:0; width:24px; z-index:9998;
            background:linear-gradient(90deg,rgba(95,37,159,0.15),transparent);
            opacity:0; transition:opacity 0.2s; pointer-events:none;
        }
        #swipe-back.active{ opacity:1; }
    </style>
</head>
<body>
    <!-- Swipe back indicator -->
    <div id="swipe-back"></div>

    <!-- Global Page Loader -->
    <div id="global-loader" aria-hidden="true">
        <div class="global-loader-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                <path d="M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7l-5.5 2.9 1-6.2L3 8.6l6.2-.9L12 2Z"/>
            </svg>
        </div>
        <div class="loader-ring" style="position:relative; width:52px; height:52px;"></div>
        <div class="loader-text">Please wait…</div>
        <div class="loader-dots"><span></span><span></span><span></span></div>
    </div>

    <!-- Install Banner -->
    <div id="install-banner">
        <div class="ib-icon"><svg viewBox="0 0 24 24" fill="white"><path d="M12 2l2.8 5.7 6.2.9-4.5 4.3 1 6.2L12 15.7l-5.5 2.9 1-6.2L3 8.6l6.2-.9L12 2Z"/></svg></div>
        <div class="ib-text">
            <div class="ib-title">Install QuickRecharge</div>
            <div class="ib-sub">Add to home screen for fastest access</div>
        </div>
        <button class="ib-btn" onclick="App.installPWA()">Install</button>
        <button class="ib-close" onclick="App.dismissInstall()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>

    <!-- App Shell -->
    <div id="app" class="screen">
        <div class="boot-dots"><span></span><span></span><span></span></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/router.js"></script>
    <script>
        // Initialize App
        document.addEventListener('DOMContentLoaded', function() {
            App.init();
            App.initMobile();
        });
    </script>

    <!-- Register Service Worker + PWA Install -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('pwa/sw.js')
                    .then(function(reg) {
                        console.log('SW registered:', reg.scope);
                    })
                    .catch(function(err) {
                        console.log('SW registration failed:', err);
                    });
            });
        }

        // PWA Install prompt
        var deferredPrompt;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            // Show install banner after 5 seconds
            setTimeout(function() {
                var dismissed = localStorage.getItem('install_dismissed');
                if (!dismissed) {
                    document.getElementById('install-banner').classList.add('show');
                }
            }, 5000);
        });

        window.addEventListener('appinstalled', function() {
            deferredPrompt = null;
            document.getElementById('install-banner').classList.remove('show');
        });
    </script>
</body>
</html>
