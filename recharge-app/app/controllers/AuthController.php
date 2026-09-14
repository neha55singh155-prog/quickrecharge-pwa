<?php
/**
 * Auth Controller
 * Handles authentication-related operations
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    /**
     * Verify mobile number
     */
    public static function verifyNumber($data) {
        $mobile = isset($data['mobile']) ? preg_replace('/[^0-9]/', '', $data['mobile']) : '';
        
        // Validate mobile
        if (!User::validateMobile($mobile)) {
            return [
                'status' => 'error',
                'message' => 'Invalid mobile number'
            ];
        }
        
        // Detect operator
        $operator = Operator::detectFromMobile($mobile);
        
        if (!$operator) {
            return [
                'status' => 'error',
                'message' => 'Could not detect operator'
            ];
        }
        
        // Get or create user
        $userId = User::getOrCreate($mobile);
        
        return [
            'status' => 'success',
            'data' => [
                'mobile' => User::formatMobile($mobile),
                'operator' => $operator,
                'user_id' => $userId
            ]
        ];
    }
    
    /**
     * Login with mobile (send OTP)
     */
    public static function sendOtp($mobile) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        if (!User::validateMobile($mobile)) {
            return [
                'status' => 'error',
                'message' => 'Invalid mobile number'
            ];
        }
        
        // Generate OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in session
        Auth::startSession();
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_mobile'] = $mobile;
        $_SESSION['otp_time'] = time();
        
        // In production, send OTP via SMS
        // For demo, just return success
        
        return [
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'data' => [
                'mobile' => User::formatMobile($mobile),
                'otp' => $otp // Remove in production
            ]
        ];
    }
    
    /**
     * Verify OTP
     */
    public static function verifyOtp($mobile, $otp) {
        Auth::startSession();
        
        $storedOtp = $_SESSION['otp'] ?? null;
        $storedMobile = $_SESSION['otp_mobile'] ?? null;
        $otpTime = $_SESSION['otp_time'] ?? 0;
        
        // Check OTP expiry (5 minutes)
        if (time() - $otpTime > 300) {
            return [
                'status' => 'error',
                'message' => 'OTP expired'
            ];
        }
        
        // Verify OTP
        if ($otp !== $storedOtp || $mobile !== $storedMobile) {
            return [
                'status' => 'error',
                'message' => 'Invalid OTP'
            ];
        }
        
        // Get or create user
        $userId = User::getOrCreate($mobile);
        
        // Set session
        Auth::setUser($userId, $mobile);
        
        // Clear OTP
        unset($_SESSION['otp'], $_SESSION['otp_mobile'], $_SESSION['otp_time']);
        
        return [
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user_id' => $userId,
                'mobile' => User::formatMobile($mobile)
            ]
        ];
    }
    
    /**
     * Get current user
     */
    public static function getCurrentUser() {
        if (!Auth::isLoggedIn()) {
            return null;
        }
        
        return User::getById(Auth::getUserId());
    }
    
    /**
     * Logout
     */
    public static function logout() {
        Auth::logout();
        
        return [
            'status' => 'success',
            'message' => 'Logged out successfully'
        ];
    }
}
