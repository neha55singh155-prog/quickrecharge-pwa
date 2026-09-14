<?php
/**
 * User Model
 * Handles user-related database operations
 */

class User {
    /**
     * Get user by ID
     */
    public static function getById($id) {
        return Database::fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Get user by mobile number
     */
    public static function getByMobile($mobile) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        return Database::fetchOne(
            "SELECT * FROM users WHERE mobile = ?",
            [$mobile]
        );
    }
    
    /**
     * Create new user
     */
    public static function create($mobile, $name = null, $email = null) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        return Database::insert(
            "INSERT INTO users (mobile, name, email) VALUES (?, ?, ?)",
            [$mobile, $name, $email]
        );
    }
    
    /**
     * Update user
     */
    public static function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email'])) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return 0;
        }
        
        $values[] = $id;
        
        return Database::update(
            "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    /**
     * Get or create user by mobile
     */
    public static function getOrCreate($mobile) {
        $user = self::getByMobile($mobile);
        
        if ($user) {
            return $user['id'];
        }
        
        return self::create($mobile);
    }
    
    /**
     * Get user order count
     */
    public static function getOrderCount($userId) {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = ?",
            [$userId]
        );
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Get user total spent
     */
    public static function getTotalSpent($userId) {
        $result = Database::fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM orders WHERE user_id = ? AND status = 'success'",
            [$userId]
        );
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Get user recent orders
     */
    public static function getRecentOrders($userId, $limit = 5) {
        return Database::fetchAll(
            "SELECT o.*, p.data, p.calls, p.sms, p.validity, op.name as operator_name, op.code as operator_code
             FROM orders o
             JOIN plans p ON o.plan_id = p.id
             JOIN operators op ON o.operator_id = op.id
             WHERE o.user_id = ?
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$userId, $limit]
        );
    }
    
    /**
     * Validate mobile number
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
