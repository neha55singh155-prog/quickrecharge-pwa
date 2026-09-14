<?php
/**
 * API: Verify Number (RATE LIMITED)
 * POST /api/verify-number.php
 */

require_once __DIR__ . '/../app/helpers/database.php';
require_once __DIR__ . '/../app/helpers/response.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/rate-limiter.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Rate limit: 15 verifications per minute per IP
if (RateLimiter::isRateLimited('verify-number', 15, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait.']);
    exit;
}

// Get input
$input = Response::getInput();

// Validate mobile
$mobile = isset($input['mobile']) ? preg_replace('/[^0-9]/', '', $input['mobile']) : '';

if (!Auth::validateMobile($mobile)) {
    Response::validation(['mobile' => 'Invalid mobile number']);
}

// Format mobile
$formattedMobile = Auth::formatMobile($mobile);

// Detect operator (simplified logic)
$operator = detectOperator($mobile);

if (!$operator) {
    Response::error('Could not detect operator', 400);
}

// Response
Response::success([
    'mobile' => $formattedMobile,
    'operator' => $operator,
    'circle' => 'Delhi',
]);

/**
 * Detect operator from mobile number prefix
 */
function detectOperator($mobile) {
    // Prefix-based operator detection (simplified)
    $prefixes = [
        'jio' => ['70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89'],
        'airtel' => ['80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'],
        'vi' => ['70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89'],
        'bsnl' => ['94', '95', '96', '97', '98', '99'],
    ];
    
    $firstTwo = substr($mobile, 0, 2);
    
    // Simple detection based on common patterns
    if (in_array($firstTwo, ['70', '71', '72', '73', '74', '75', '76', '77', '78', '79'])) {
        return [
            'id' => 1,
            'name' => 'Jio',
            'code' => 'jio',
            'logo' => 'assets/icons/jio.svg'
        ];
    }
    
    if (in_array($firstTwo, ['80', '81', '82', '83', '84', '85', '86', '87', '88', '89'])) {
        return [
            'id' => 2,
            'name' => 'Airtel',
            'code' => 'airtel',
            'logo' => 'assets/icons/airtel.svg'
        ];
    }
    
    if (in_array($firstTwo, ['90', '91', '92', '93'])) {
        return [
            'id' => 3,
            'name' => 'Vi',
            'code' => 'vi',
            'logo' => 'assets/icons/vi.svg'
        ];
    }
    
    if (in_array($firstTwo, ['94', '95', '96', '97'])) {
        return [
            'id' => 4,
            'name' => 'BSNL',
            'code' => 'bsnl',
            'logo' => 'assets/icons/bsnl.svg'
        ];
    }
    
    // Default to Jio
    return [
        'id' => 1,
        'name' => 'Jio',
        'code' => 'jio',
        'logo' => 'assets/icons/jio.svg'
    ];
}
