<?php
/**
 * Rate Limiting Helper
 * File-based rate limiting for API endpoints
 */
class RateLimiter {
    private static $dataDir;

    public static function init($dataDir = null) {
        self::$dataDir = $dataDir ?: sys_get_temp_dir();
    }

    /**
     * Check if request is rate limited
     * @param string $key Identifier (e.g., 'create-order', 'verify-payment')
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $windowSeconds Time window in seconds
     * @return bool true if rate limited, false if allowed
     */
    public static function isRateLimited($key, $maxAttempts = 10, $windowSeconds = 60) {
        $identifier = self::getIdentifier($key);
        $file = self::$dataDir . '/rate_limit_' . md5($identifier) . '.json';

        $data = ['attempts' => [], 'blocked_until' => 0];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true) ?: $data;
        }

        $now = time();

        // Check if currently blocked
        if (!empty($data['blocked_until']) && $data['blocked_until'] > $now) {
            return true;
        }

        // Clean old attempts
        $data['attempts'] = array_filter($data['attempts'], function($ts) use ($now, $windowSeconds) {
            return ($now - $ts) < $windowSeconds;
        });

        // Check limit
        if (count($data['attempts']) >= $maxAttempts) {
            $data['blocked_until'] = $now + $windowSeconds;
            file_put_contents($file, json_encode($data), LOCK_EX);
            return true;
        }

        // Record attempt
        $data['attempts'][] = $now;
        file_put_contents($file, json_encode($data), LOCK_EX);
        return false;
    }

    /**
     * Get client identifier
     */
    private static function getIdentifier($key) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $session = session_id() ?: '';
        return $key . '_' . $ip . '_' . $session;
    }

    /**
     * Get remaining attempts
     */
    public static function getRemainingAttempts($key, $maxAttempts = 10, $windowSeconds = 60) {
        $identifier = self::getIdentifier($key);
        $file = self::$dataDir . '/rate_limit_' . md5($identifier) . '.json';

        $data = ['attempts' => []];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true) ?: $data;
        }

        $now = time();
        $data['attempts'] = array_filter($data['attempts'], function($ts) use ($now, $windowSeconds) {
            return ($now - $ts) < $windowSeconds;
        });

        return max(0, $maxAttempts - count($data['attempts']));
    }
}

// Initialize with temp directory
RateLimiter::init();
