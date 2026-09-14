<?php
/**
 * API: Report / verify payment (HARDENED)
 * POST /api/verify-payment.php
 * Body: { transaction_id, status?: PENDING|FAILED|CANCELLED, upi_reference? }
 *
 * SECURITY RULES:
 * - Frontend can NEVER set SUCCESS. SUCCESS comes only from:
 *    a) real gateway webhook (api/payment-webhook.php), or
 *    b) logged-in admin verification with UTR.
 * - Frontend reports PENDING (paid, awaiting confirmation), FAILED, CANCELLED.
 * - SUCCESS transactions are idempotent: repeated calls do not double-recharge.
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';
require_once $basePath . '/app/services/PaymentService.php';
require_once $basePath . '/app/helpers/rate-limiter.php';

// Rate limit: 20 verifications per minute per IP
if (RateLimiter::isRateLimited('verify-payment', 20, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait.']);
    exit;
}
require_once $basePath . '/admin/data/store.php';
require_once $basePath . '/app/services/PaymentService.php';

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$txnId = trim($input['transaction_id'] ?? '');
$status = strtoupper(trim($input['status'] ?? 'PENDING'));
$utr = trim($input['upi_reference'] ?? $input['reference_id'] ?? '');

if ($txnId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Transaction ID required']);
    exit;
}

$store = new DataStore($basePath . '/admin/data');
$store->expireStaleTransactions();
$txn = $store->getTransaction($txnId);
if (!$txn) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Transaction not found']);
    exit;
}

// Idempotency: terminal SUCCESS stays SUCCESS, never re-process
if ($txn['status'] === 'SUCCESS') {
    echo json_encode(['success' => true, 'data' => [
        'transaction_id' => $txn['transaction_id'],
        'status' => 'SUCCESS',
        'amount' => (float)($txn['plan_amount'] ?? $txn['amount'] ?? 0),
        'mobile' => $txn['mobile'],
        'operator' => $txn['operator'],
    ], 'note' => 'Already completed — duplicate ignored']);
    exit;
}
if (in_array($txn['status'], ['EXPIRED'], true) && $status !== 'FAILED') {
    http_response_code(410);
    echo json_encode(['success' => false, 'error' => 'Payment session expired. Please create a new payment request.']);
    exit;
}

// Frontend-allowed statuses only. SUCCESS via this endpoint requires admin session.
$frontendAllowed = ['PENDING', 'FAILED', 'CANCELLED'];
if ($status === 'SUCCESS') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $isAdmin = !empty($_SESSION['admin_logged']);
    if (!$isAdmin) {
        // Do not trust frontend success — record as PENDING verification
        $store->updateTransactionStatus($txnId, 'PENDING', $utr);
        PaymentService::log($store, 'verification.frontend_success_rejected', ['transaction_id' => $txnId]);
        echo json_encode(['success' => true, 'data' => [
            'transaction_id' => $txnId, 'status' => 'PENDING',
        ], 'note' => 'Payment received — waiting for backend verification']);
        exit;
    }
    // Admin-verified success
    $store->updateTransactionStatus($txnId, 'SUCCESS', $utr);
    PaymentService::log($store, 'verification.admin_success', ['transaction_id' => $txnId, 'utr' => $utr]);
} else {
    if (!in_array($status, $frontendAllowed, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }
    if ($utr !== '') $store->setTransactionUtr($txnId, $utr);
    if ($status !== 'PENDING') $store->updateTransactionStatus($txnId, $status, $utr);
    else $store->updateTransactionStatus($txnId, 'PENDING', $utr);
    PaymentService::log($store, 'verification.' . strtolower($status), ['transaction_id' => $txnId]);
}

$txn = $store->getTransaction($txnId);
echo json_encode(['success' => true, 'data' => [
    'transaction_id' => $txn['transaction_id'],
    'status' => $txn['status'],
    'amount' => (float)($txn['plan_amount'] ?? $txn['amount'] ?? 0),
    'mobile' => $txn['mobile'],
    'operator' => $txn['operator'],
]]);
