<?php
/**
 * API: Create Order + Transaction (SECURE)
 * POST /api/create-order.php
 * Body: { mobile, plan_id, operator, upi_app }
 *
 * SECURITY: client `amount` is IGNORED. Server resolves price from DB.
 * Creates order + INITIATED transaction with unique reference, server amount,
 * admin UPI settings and configured expiry. Duplicate SUCCESS protection
 * handled at verify time via idempotent transaction_id.
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

// Rate limit: 10 orders per minute per IP
if (RateLimiter::isRateLimited('create-order', 10, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait a moment.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$mobile = PaymentService::normalizeMobile($input['mobile'] ?? '');
$planId = (int)($input['plan_id'] ?? 0);
$operator = strtolower(trim($input['operator'] ?? ''));
$upiApp = strtolower(trim($input['upi_app'] ?? ''));

if (!$mobile) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid 10-digit mobile number']);
    exit;
}
if ($planId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please select a valid plan']);
    exit;
}
if (!PaymentService::validOperator($operator)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid operator']);
    exit;
}
if (!in_array($upiApp, PaymentService::ALLOWED_METHODS, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unsupported payment method']);
    exit;
}

$store = new DataStore($basePath . '/admin/data');

if (!$store->isMethodEnabled($upiApp)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'This payment method is currently disabled']);
    exit;
}

// Authoritative plan lookup — never trust client amount
$plan = $store->getPlan($planId);
if (!$plan || empty($plan['active'])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Selected plan is unavailable']);
    exit;
}
if (strtolower($plan['operator'] ?? '') !== $operator) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Plan does not belong to this operator']);
    exit;
}
$amount = (float)$plan['amount'];

$ck = $store->getCheckout();
$upiId = trim($ck['upi_id'] ?? '');
$merchantName = $ck['merchant_display_name'] ?? $ck['merchant_name'] ?? 'QuickRecharge';
$currency = $ck['currency'] ?? 'INR';
$note = ($ck['description'] ?? 'Mobile Recharge') . ' ' . $mobile;
if (!PaymentService::validVpa($upiId)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Configured UPI ID is invalid. Please update it in Admin → Payment Settings with your real UPI ID (e.g. yourname@okhdfc).']);
    exit;
}

$expiresIn = $store->getQrExpirySeconds();

$planDesc = trim(($plan['data'] ?? '') . ', ' . ($plan['validity'] ?? ''), ', ');
$orderId = $store->createOrder([
    'mobile' => $mobile,
    'operator' => $operator,
    'amount' => $amount,
    'plan' => $planDesc,
]);

$txnId = $store->createTransaction([
    'reference_id' => '',
    'mobile' => $mobile,
    'operator' => $operator,
    'plan_id' => $planId,
    'plan_amount' => $amount,
    'upi_id' => $upiId,
    'payment_method' => $upiApp,
    'expires_in' => $expiresIn,
]);

PaymentService::log($store, 'transaction.created', [
    'transaction_id' => $txnId, 'mobile' => $mobile,
    'plan_id' => $planId, 'amount' => $amount, 'method' => $upiApp,
]);

$upiUri = PaymentService::buildUpiUri($upiId, $merchantName, $amount, $txnId, $note, $currency);

echo json_encode(['success' => true, 'data' => [
    'order_id' => $orderId,
    'transaction_id' => $txnId,
    'amount' => $amount,
    'upi_id' => $upiId,
    'merchant_name' => $merchantName,
    'currency' => $currency,
    'upi_uri' => $upiUri,
    'app_uri' => PaymentService::appDeepLink($upiApp, $upiUri),
    'expires_in' => $expiresIn,
    'plan' => $plan,
]]);
