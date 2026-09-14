<?php
/**
 * PaymentService — isolated payment integration layer.
 *
 * All UPI URI building, validation, logging and verification live here
 * so merchant/provider credentials can be swapped without touching
 * checkout or API code.
 *
 * Current mode: MANUAL_UPI (collect via merchant VPA + dynamic QR/intent).
 * Server-side SUCCESS is granted only by:
 *   1. A real gateway webhook (see api/payment-webhook.php), or
 *   2. Explicit admin verification (admin session) with UTR reference.
 * Frontend reports of SUCCESS are NEVER trusted.
 */
class PaymentService {
    const ALLOWED_METHODS = ['phonepe', 'gpay', 'paytm', 'qr'];
    const STATUSES = ['INITIATED', 'PENDING', 'SUCCESS', 'FAILED', 'EXPIRED', 'CANCELLED'];

    /** Validate + normalize Indian mobile number. Returns normalized or false. */
    public static function normalizeMobile($raw) {
        $digits = preg_replace('/[^0-9]/', '', (string)$raw);
        // Allow +91 prefix
        if (strlen($digits) === 12 && substr($digits, 0, 2) === '91') {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        if (!preg_match('/^[6-9]\d{9}$/', $digits)) return false;
        return $digits;
    }

    public static function validOperator($op) {
        return in_array(strtolower((string)$op), ['jio', 'airtel', 'vi', 'bsnl'], true);
    }

    /**
     * VPA must be a real UPI ID like name@okhdfc / 98765@paytm.
     * Format check only — existence is verified by the UPI network at pay time.
     */
    public static function validVpa($vpa) {
        return (bool)preg_match('/^[\w.\-]{2,256}@[a-zA-Z]{2,64}$/', (string)$vpa);
    }

    /** Build UPI collect URI. Amount MUST be server-resolved. */
    public static function buildUpiUri($pa, $pn, $amount, $txnRef, $note, $currency = 'INR') {
        $params = http_build_query([
            'pa' => $pa,
            'pn' => $pn,
            'am' => number_format((float)$amount, 2, '.', ''),
            'cu' => $currency,
            'tr' => $txnRef,
            'tn' => substr($note, 0, 80),
        ]);
        return 'upi://pay?' . $params;
    }

    /** Per-app deep link. Falls back to generic upi://pay. */
    public static function appDeepLink($app, $upiUri) {
        $query = parse_url($upiUri, PHP_URL_QUERY);
        switch ($app) {
            case 'phonepe': return 'phonepe://pay?' . $query;
            case 'gpay':    return 'tez://upi/pay?' . $query; // official GPay UPI intent host
            case 'paytm':   return 'paytmmp://pay?' . $query;
            case 'qr':
            default:        return $upiUri;
        }
    }

    /**
     * Verification hook for a real gateway.
     * Return ['verified' => bool, 'status' => string].
     * MANUAL mode: never auto-verifies — human/admin confirms against
     * bank statement using transaction_id + UTR.
     */
    public static function verifyWithProvider($txn, $referenceId = '') {
        // TODO: plug Razorpay/PhonePe/Cashfree verification here using
        // server-side env credentials. Never expose secrets to frontend.
        return ['verified' => false, 'status' => 'PENDING', 'mode' => 'MANUAL_UPI'];
    }

    public static function log($store, $event, $context = []) {
        if (method_exists($store, 'addLog')) {
            $store->addLog($event, $context);
        } else {
            error_log('[payment] ' . $event . ' ' . json_encode($context));
        }
    }
}
