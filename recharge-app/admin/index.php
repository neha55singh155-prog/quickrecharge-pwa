<?php
ob_start();

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';

// --- Secure session configuration ---
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // HTTPS enforced on Hostinger
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', 1800);
    session_start();
}

// --- Admin credentials (hashed) ---
// Default password: admin123 — CHANGE THIS IN PRODUCTION
// Generate new hash: php -r "echo password_hash('your_password', PASSWORD_DEFAULT);"
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', '$2y$12$10MIyOrVMxbu5l06SccGK.2xzZCCTrCK03yr.7JrCs2qBsO/uwomu'); // Default: admin123 — CHANGE IN PRODUCTION
define('ADMIN_LOCKOUT_SECONDS', 900); // 15 minutes lockout after max attempts
define('ADMIN_MAX_ATTEMPTS', 5);

$currentPage = isset($_GET['page']) ? preg_replace('/[^a-z\-]/', '', $_GET['page']) : 'dashboard';
$action = isset($_GET['action']) ? preg_replace('/[^a-z\-]/', '', $_GET['action']) : null;

// --- Login rate limiting ---
function getLoginAttempts() {
    $attemptsFile = sys_get_temp_dir() . '/admin_login_attempts.json';
    if (!file_exists($attemptsFile)) return ['count' => 0, 'last_attempt' => 0];
    $data = json_decode(file_get_contents($attemptsFile), true);
    return $data ?: ['count' => 0, 'last_attempt' => 0];
}

function recordLoginAttempt() {
    $data = getLoginAttempts();
    $data['count']++;
    $data['last_attempt'] = time();
    file_put_contents(sys_get_temp_dir() . '/admin_login_attempts.json', json_encode($data));
}

function clearLoginAttempts() {
    file_put_contents(sys_get_temp_dir() . '/admin_login_attempts.json', json_encode(['count' => 0, 'last_attempt' => 0]));
}

function isLoginLocked() {
    $data = getLoginAttempts();
    if ($data['count'] >= ADMIN_MAX_ATTEMPTS && (time() - $data['last_attempt']) < ADMIN_LOCKOUT_SECONDS) {
        return true;
    }
    if ($data['count'] >= ADMIN_MAX_ATTEMPTS && (time() - $data['last_attempt']) >= ADMIN_LOCKOUT_SECONDS) {
        clearLoginAttempts();
    }
    return false;
}

// --- CSRF token generation/verification ---
function generateAdminCsrfToken() {
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

function verifyAdminCsrfToken($token) {
    return !empty($_SESSION['admin_csrf_token']) && hash_equals($_SESSION['admin_csrf_token'], $token);
}

// --- Audit logging ---
function auditLog($event, $context = []) {
    $logFile = $basePath . '/admin/data/audit_log.json';
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true) ?: [];
    }
    $logs[] = [
        'at' => date('Y-m-d H:i:s'),
        'event' => $event,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'context' => $context,
    ];
    if (count($logs) > 5000) $logs = array_slice($logs, -5000);
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
}

// --- Require admin authentication ---
function requireAdmin() {
    if (empty($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
        header('Location: ?page=login');
        exit;
    }
}

// --- Login page ---
if ($currentPage === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyAdminCsrfToken($token)) {
            $error = 'Invalid request. Please try again.';
            include __DIR__ . '/pages/login.php';
            exit;
        }

        if (isLoginLocked()) {
            $error = 'Too many failed attempts. Please wait 15 minutes.';
            include __DIR__ . '/pages/login.php';
            exit;
        }

        $user = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';

        // Use password_verify for secure comparison
        if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $user;
            $_SESSION['admin_login_time'] = time();
            clearLoginAttempts();
            auditLog('admin.login.success', ['user' => $user]);
            header('Location: ?page=dashboard');
            exit;
        }

        recordLoginAttempt();
        auditLog('admin.login.failed', ['user' => $user]);
        $error = 'Invalid credentials';
    }
    generateAdminCsrfToken();
    include __DIR__ . '/pages/login.php';
    exit;
}

// --- All other pages require authentication ---
requireAdmin();

// --- Session timeout check (30 minutes) ---
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > 1800) {
    session_unset();
    session_destroy();
    header('Location: ?page=login&expired=1');
    exit;
}
$_SESSION['admin_login_time'] = time();

// --- Logout ---
if ($currentPage === 'logout') {
    auditLog('admin.logout', ['user' => $_SESSION['admin_user'] ?? 'unknown']);
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
    header('Location: ?page=login');
    exit;
}

$store = new DataStore($basePath . '/admin/data');
$stats = $store->getStats();

// --- Generate CSRF token for forms ---
generateAdminCsrfToken();

// --- Enforce POST for all state-changing operations ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verifyAdminCsrfToken($csrfToken)) {
        http_response_code(403);
        echo 'CSRF token validation failed.';
        exit;
    }
}

// Handle POST redirects BEFORE any HTML output
if (($currentPage === 'operators' && ($action === 'add' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') ||
    ($currentPage === 'plans' && ($action === 'add' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST')) {
    include __DIR__ . '/pages/' . ($currentPage === 'operators' ? 'operator-form.php' : 'plan-form.php');
    ob_end_flush();
    exit;
}

// --- DELETE operations: POST only with CSRF ---
if ($action === 'delete' && in_array($currentPage, ['plans', 'operators'])) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=' . $currentPage);
        ob_end_flush();
        exit;
    }
    $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($id <= 0) {
        header('Location: ?page=' . $currentPage);
        ob_end_flush();
        exit;
    }
    if ($currentPage === 'plans') {
        $store->deletePlan($id);
        auditLog('admin.plan.delete', ['id' => $id]);
    }
    if ($currentPage === 'operators') {
        $store->deleteOperator($id);
        auditLog('admin.operator.delete', ['id' => $id]);
    }
    header('Location: ?page=' . $currentPage);
    ob_end_flush();
    exit;
}

include __DIR__ . '/pages/layout-header.php';

switch ($currentPage) {
    case 'dashboard':
        include __DIR__ . '/pages/dashboard.php';
        break;
    case 'plans':
        if ($action === 'add' || $action === 'edit') {
            include __DIR__ . '/pages/plan-form.php';
        } else {
            include __DIR__ . '/pages/plans.php';
        }
        break;
    case 'operators':
        if ($action === 'add' || $action === 'edit') {
            include __DIR__ . '/pages/operator-form.php';
        } else {
            include __DIR__ . '/pages/operators.php';
        }
        break;
    case 'home-content':
        include __DIR__ . '/pages/home-content.php';
        break;
    case 'settings':
        include __DIR__ . '/pages/settings.php';
        break;
    case 'payment-settings':
        include __DIR__ . '/pages/payment-settings.php';
        break;
    case 'transactions':
        include __DIR__ . '/pages/orders.php';
        break;
    default:
        include __DIR__ . '/pages/dashboard.php';
}

include __DIR__ . '/pages/layout-footer.php';
ob_end_flush();
?>
