<?php
/**
 * API: Transaction status (server truth for polling)
 * GET /api/transaction-status.php?transaction_id=RCH-...
 * Auto-expires stale transactions. Never trusts client amount.
 */
header('Content-Type: application/json');

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';

$txnId = trim($_GET['transaction_id'] ?? '');
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

$expiresIn = 0;
if (!empty($txn['expires_at'])) {
    $expiresIn = max(0, strtotime($txn['expires_at']) - time());
}

echo json_encode(['success' => true, 'data' => [
    'transaction_id' => $txn['transaction_id'],
    'status' => $txn['status'],
    'amount' => (float)($txn['plan_amount'] ?? $txn['amount'] ?? 0),
    'mobile' => $txn['mobile'],
    'operator' => $txn['operator'],
    'plan_id' => (int)($txn['plan_id'] ?? 0),
    'payment_method' => $txn['payment_method'] ?? '',
    'expires_in' => $expiresIn,
    'expires_at' => $txn['expires_at'] ?? null,
]]);
