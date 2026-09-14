<?php
/**
 * Order Model
 * Handles order-related database operations
 */

class Order {
    /**
     * Get order by ID
     */
    public static function getById($id) {
        return Database::fetchOne(
            "SELECT o.*, p.data, p.calls, p.sms, p.validity, p.category,
                    op.name as operator_name, op.code as operator_code,
                    u.mobile as user_mobile
             FROM orders o
             JOIN plans p ON o.plan_id = p.id
             JOIN operators op ON o.operator_id = op.id
             JOIN users u ON o.user_id = u.id
             WHERE o.id = ?",
            [$id]
        );
    }
    
    /**
     * Create new order
     */
    public static function create($userId, $planId, $mobile, $operatorId, $amount) {
        return Database::insert(
            "INSERT INTO orders (user_id, plan_id, mobile, operator_id, amount, status) 
             VALUES (?, ?, ?, ?, ?, 'pending')",
            [$userId, $planId, $mobile, $operatorId, $amount]
        );
    }
    
    /**
     * Update order status
     */
    public static function updateStatus($id, $status, $referenceId = null) {
        $sql = "UPDATE orders SET status = ?";
        $params = [$status];
        
        if ($referenceId) {
            $sql .= ", reference_id = ?";
            $params[] = $referenceId;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return Database::update($sql, $params);
    }
    
    /**
     * Get user orders
     */
    public static function getByUserId($userId, $limit = 20, $offset = 0) {
        return Database::fetchAll(
            "SELECT o.*, p.data, p.calls, p.sms, p.validity,
                    op.name as operator_name, op.code as operator_code
             FROM orders o
             JOIN plans p ON o.plan_id = p.id
             JOIN operators op ON o.operator_id = op.id
             WHERE o.user_id = ?
             ORDER BY o.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }
    
    /**
     * Get orders by status
     */
    public static function getByStatus($status, $limit = 50) {
        return Database::fetchAll(
            "SELECT o.*, p.data, p.calls, p.sms, p.validity,
                    op.name as operator_name, op.code as operator_code,
                    u.mobile as user_mobile
             FROM orders o
             JOIN plans p ON o.plan_id = p.id
             JOIN operators op ON o.operator_id = op.id
             JOIN users u ON o.user_id = u.id
             WHERE o.status = ?
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$status, $limit]
        );
    }
    
    /**
     * Get recent orders
     */
    public static function getRecent($limit = 10) {
        return Database::fetchAll(
            "SELECT o.*, p.data, p.calls, p.sms, p.validity,
                    op.name as operator_name, op.code as operator_code,
                    u.mobile as user_mobile
             FROM orders o
             JOIN plans p ON o.plan_id = p.id
             JOIN operators op ON o.operator_id = op.id
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Get order count by status
     */
    public static function getCountByStatus($status) {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM orders WHERE status = ?",
            [$status]
        );
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Get total revenue
     */
    public static function getTotalRevenue() {
        $result = Database::fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM orders WHERE status = 'success'"
        );
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Cancel order
     */
    public static function cancel($id) {
        return self::updateStatus($id, 'cancelled');
    }
    
    /**
     * Check if order can be cancelled
     */
    public static function canCancel($id) {
        $order = self::getById($id);
        
        if (!$order) {
            return false;
        }
        
        return in_array($order['status'], ['pending', 'processing']);
    }
    
    /**
     * Generate receipt
     */
    public static function generateReceipt($id) {
        $order = self::getById($id);
        
        if (!$order || $order['status'] !== 'success') {
            return null;
        }
        
        return [
            'order_id' => $order['id'],
            'mobile' => $order['mobile'],
            'operator' => $order['operator_name'],
            'amount' => $order['amount'],
            'plan' => "{$order['data']} {$order['calls']} Calls",
            'validity' => $order['validity'],
            'transaction_id' => $order['reference_id'],
            'date' => date('Y-m-d H:i:s', strtotime($order['created_at'])),
            'status' => 'Success'
        ];
    }
}
