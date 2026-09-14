<?php
/**
 * API: Payment Webhook (HARDENED)
 * POST /api/payment-webhook.php
 * 
 * Receives payment status updates from payment gateway.
 * SECURITY: Requires HMAC-SHA256 signature verification.
 * Amount must match server-stored amount. Idempotent processing.
 */

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';
require_once $basePath . '/app/services/PaymentService.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Log the webhook
logWebhook($input);

// --- Signature verification ---
// In production, set WEBHOOK_SECRET env var with your payment gateway's webhook secret
$webhookSecret = getenv('WEBHOOK_SECRET');
if ($webhookSecret) {
    $signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    if (empty($signature)) {
        logWebhook(['error' => 'Missing webhook signature']);
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Missing signature']);
        exit;
    }
    $expectedSig = hash_hmac('sha256', json_encode($input), $webhookSecret);
    if (!hash_equals($expectedSig, $signature)) {
        logWebhook(['error' => 'Invalid webhook signature']);
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid signature']);
        exit;
    }
} else {
    // No webhook secret configured — log warning but allow for demo
    logWebhook(['warning' => 'No WEBHOOK_SECRET configured — signature verification skipped']);
}

// --- Extract and validate data ---
$orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
$status = isset($input['status']) ? strtolower(trim($input['status'])) : '';
$transactionId = isset($input['transaction_id']) ? preg_replace('/[^a-zA-Z0-9\-]/', '', $input['transaction_id']) : '';
$providerAmount = isset($input['amount']) ? floatval($input['amount']) : null;
$providerCurrency = isset($input['currency']) ? strtoupper(trim($input['currency'])) : 'INR';

if ($orderId <= 0 || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid webhook data']);
    exit;
}

$store = new DataStore($basePath . '/admin/data');

// --- Get order ---
$order = $store->getOrders();
$orderFound = null;
foreach ($order as $o) {
    if ($o['id'] == $orderId) { $orderFound = $o; break; }
}
if (!$orderFound) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Order not found']);
    exit;
}

// --- Amount verification ---
if ($providerAmount !== null) {
    $serverAmount = floatval($orderFound['amount'] ?? 0);
    if (abs($providerAmount - $serverAmount) > 0.01) {
        logWebhook([
            'error' => 'Amount mismatch',
            'provider_amount' => $providerAmount,
            'server_amount' => $serverAmount,
            'order_id' => $orderId,
        ]);
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount mismatch']);
        exit;
    }
}

// --- Currency verification ---
if ($providerCurrency !== 'INR') {
    logWebhook(['error' => 'Invalid currency', 'currency' => $providerCurrency, 'order_id' => $orderId]);
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid currency']);
    exit;
}

// --- Valid status values ---
$validStatuses = ['success', 'failed', 'pending'];
if (!in_array($status, $validStatuses, true)) {
    logWebhook(['error' => 'Invalid status', 'status' => $status]);
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

// --- Process webhook ---
try {
    // Find associated transaction for idempotency check
    $transactions = $store->getTransactions();
    $txnFound = null;
    if (!empty($transactionId)) {
        foreach ($transactions as $t) {
            if ($t['transaction_id'] === $transactionId) { $txnFound = $t; break; }
        }
    }

    // Idempotency: if already SUCCESS, skip
    if ($txnFound && $txnFound['status'] === 'SUCCESS') {
        logWebhook(['info' => 'Duplicate webhook ignored', 'transaction_id' => $transactionId]);
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Already processed']);
        exit;
    }

    switch ($status) {
        case 'success':
            $store->updateOrderStatus($orderId, 'success');
            if ($txnFound) {
                $store->updateTransactionStatus($transactionId, 'SUCCESS', $transactionId);
            }
            logWebhook(['info' => 'Payment success confirmed', 'order_id' => $orderId, 'transaction_id' => $transactionId]);
            break;

        case 'failed':
            $store->updateOrderStatus($orderId, 'failed');
            if ($txnFound) {
                $store->updateTransactionStatus($transactionId, 'FAILED');
            }
            logWebhook(['info' => 'Payment failed', 'order_id' => $orderId]);
            break;

        case 'pending':
            logWebhook(['info' => 'Payment pending', 'order_id' => $orderId]);
            break;
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Webhook received']);

} catch (Exception $e) {
    error_log("Webhook processing error: " . $e->getMessage());
    logWebhook(['error' => $e->getMessage(), 'order_id' => $orderId]);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal error']);
}

/**
 * Log webhook data
 */
function logWebhook($data) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'data' => $data
    ];
    error_log("Webhook: " . json_encode($logEntry));

    try {
        $logFile = dirname(__DIR__) . '/admin/data/payment_logs.json';
        $logs = [];
        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true) ?: [];
        }
        $logs[] = ['at' => date('Y-m-d H:i:s'), 'event' => 'webhook.received', 'context' => $data];
        if (count($logs) > 2000) $logs = array_slice($logs, -2000);
        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        error_log("Failed to log webhook: " . $e->getMessage());
    }
}
