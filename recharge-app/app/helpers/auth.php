<?php
/**
 * Auth Helper
 * Session management and user authentication
 */

class Auth {
    /**
     * Start session if not started
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user mobile
     */
    public static function getMobile() {
        self::startSession();
        return $_SESSION['user_mobile'] ?? null;
    }
    
    /**
     * Set user session
     */
    public static function setUser($userId, $mobile) {
        self::startSession();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_mobile'] = $mobile;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::startSession();
        session_destroy();
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken() {
        self::startSession();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token) {
        self::startSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate mobile number (Indian)
     */
    public static function validateMobile($mobile) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        return strlen($mobile) === 10 && preg_match('/^[6-9]\d{9}$/', $mobile);
    }
    
    /**
     * Format mobile number
     */
    public static function formatMobile($mobile) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($mobile) === 10) {
            return '+91' . $mobile;
        }
        return $mobile;
    }
}
