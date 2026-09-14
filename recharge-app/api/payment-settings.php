<?php
/**
 * API: Get Payment Settings (for frontend checkout)
 * GET /api/payment-settings.php
 */
header('Content-Type: application/json');

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';

$store = new DataStore($basePath . '/admin/data');
$ck = $store->getCheckout();

$apps = array_filter($ck['upi_apps'] ?? [], fn($a) => !empty($a['active']));

echo json_encode([
    'success' => true,
    'data' => [
        'upi_id' => $ck['upi_id'] ?? '',
        'merchant_name' => $ck['merchant_name'] ?? '',
        'merchant_display_name' => $ck['merchant_display_name'] ?? $ck['merchant_name'] ?? '',
        'currency' => $ck['currency'] ?? 'INR',
        'description' => $ck['description'] ?? 'Mobile Recharge',
        'upi_apps' => array_values($apps),
        'qr_image' => $ck['qr_image'] ?? '',
        'qr_expiry_minutes' => (int)($ck['qr_expiry_minutes'] ?? 5),
        'qr_expiry_seconds' => $store->getQrExpirySeconds(),
    ]
]);
