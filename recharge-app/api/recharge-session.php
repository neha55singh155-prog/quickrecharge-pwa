<?php
/**
 * API: Recharge session (server-authoritative state)
 * GET  /api/recharge-session.php          -> current session (DB-resolved plan)
 * POST /api/recharge-session.php {action:init, mobile, operator}
 * POST /api/recharge-session.php {action:select-plan, plan_id}
 * POST /api/recharge-session.php {action:reset}
 *
 * Frontend localStorage is NOT authoritative; this session is.
 */
header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';
require_once $basePath . '/app/services/PaymentService.php';

$store = new DataStore($basePath . '/admin/data');

if (!isset($_SESSION['recharge'])) $_SESSION['recharge'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $s = $_SESSION['recharge'];
    $plan = null;
    if (!empty($s['plan_id'])) {
        $plan = $store->getPlan((int)$s['plan_id']);
        if (!$plan || empty($plan['active'])) $plan = null;
    }
    echo json_encode(['success' => true, 'data' => [
        'mobile' => $s['mobile'] ?? '',
        'operator' => $s['operator'] ?? '',
        'plan_id' => (int)($s['plan_id'] ?? 0),
        'plan' => $plan,
        'amount' => $plan ? (float)$plan['amount'] : 0,
    ]]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$action = strtolower(trim($input['action'] ?? ''));

if ($action === 'init') {
    $mobile = PaymentService::normalizeMobile($input['mobile'] ?? '');
    $operator = strtolower(trim($input['operator'] ?? ''));
    if (!$mobile) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Enter a valid 10-digit mobile number']);
        exit;
    }
    if (!PaymentService::validOperator($operator)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please select a valid operator']);
        exit;
    }
    $_SESSION['recharge'] = ['mobile' => $mobile, 'operator' => $operator, 'plan_id' => 0];
    echo json_encode(['success' => true, 'data' => $_SESSION['recharge']]);
    exit;
}

if ($action === 'select-plan') {
    $planId = (int)($input['plan_id'] ?? 0);
    $plan = $store->getPlan($planId);
    if (!$plan || empty($plan['active'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Selected plan is unavailable']);
        exit;
    }
    $s = $_SESSION['recharge'] ?? [];
    if (!empty($s['operator']) && strtolower($plan['operator']) !== strtolower($s['operator'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Plan does not belong to the selected operator']);
        exit;
    }
    $_SESSION['recharge']['plan_id'] = $planId;
    if (empty($_SESSION['recharge']['operator'])) $_SESSION['recharge']['operator'] = strtolower($plan['operator']);
    echo json_encode(['success' => true, 'data' => [
        'plan_id' => $planId,
        'amount' => (float)$plan['amount'],
        'plan' => $plan,
    ]]);
    exit;
}

if ($action === 'reset') {
    $_SESSION['recharge'] = [];
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Unknown action']);
