<?php
/**
 * Payment Model
 * Handles payment-related database operations
 */

class Payment {
    /**
     * Get payment by ID
     */
    public static function getById($id) {
        return Database::fetchOne(
            "SELECT * FROM payments WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Get payment by order ID
     */
    public static function getByOrderId($orderId) {
        return Database::fetchOne(
            "SELECT * FROM payments WHERE order_id = ?",
            [$orderId]
        );
    }
    
    /**
     * Get payment by transaction ID
     */
    public static function getByTransactionId($transactionId) {
        return Database::fetchOne(
            "SELECT * FROM payments WHERE transaction_id = ?",
            [$transactionId]
        );
    }
    
    /**
     * Create new payment
     */
    public static function create($orderId, $amount, $method = 'upi') {
        return Database::insert(
            "INSERT INTO payments (order_id, amount, method, status) 
             VALUES (?, ?, ?, 'pending')",
            [$orderId, $amount, $method]
        );
    }
    
    /**
     * Update payment status
     */
    public static function updateStatus($id, $status, $transactionId = null) {
        $sql = "UPDATE payments SET status = ?";
        $params = [$status];
        
        if ($transactionId) {
            $sql .= ", transaction_id = ?";
            $params[] = $transactionId;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return Database::update($sql, $params);
    }
    
    /**
     * Mark payment as success
     */
    public static function markSuccess($id, $transactionId) {
        return self::updateStatus($id, 'success', $transactionId);
    }
    
    /**
     * Mark payment as failed
     */
    public static function markFailed($id) {
        return self::updateStatus($id, 'failed');
    }
    
    /**
     * Get user payments
     */
    public static function getByUserId($userId, $limit = 20) {
        return Database::fetchAll(
            "SELECT p.*, o.mobile, o.amount as order_amount,
                    op.name as operator_name, op.code as operator_code
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN operators op ON o.operator_id = op.id
             WHERE o.user_id = ?
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$userId, $limit]
        );
    }
    
    /**
     * Get payments by status
     */
    public static function getByStatus($status, $limit = 50) {
        return Database::fetchAll(
            "SELECT p.*, o.mobile, o.amount as order_amount,
                    op.name as operator_name, op.code as operator_code
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN operators op ON o.operator_id = op.id
             WHERE p.status = ?
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$status, $limit]
        );
    }
    
    /**
     * Get total successful payments
     */
    public static function getTotalSuccess() {
        $result = Database::fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'success'"
        );
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Process UPI payment
     */
    public static function processUpi($paymentId, $upiId) {
        $payment = self::getById($paymentId);
        
        if (!$payment) {
            return false;
        }
        
        // In production, integrate with UPI gateway
        // For demo, simulate processing
        $transactionId = 'UPI' . time() . rand(1000, 9999);
        
        return self::markSuccess($paymentId, $transactionId);
    }
    
    /**
     * Generate payment receipt
     */
    public static function generateReceipt($paymentId) {
        $payment = Database::fetchOne(
            "SELECT p.*, o.mobile, o.amount as order_amount,
                    op.name as operator_name, op.code as operator_code
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             JOIN operators op ON o.operator_id = op.id
             WHERE p.id = ?",
            [$paymentId]
        );
        
        if (!$payment || $payment['status'] !== 'success') {
            return null;
        }
        
        return [
            'payment_id' => $payment['id'],
            'order_id' => $payment['order_id'],
            'transaction_id' => $payment['transaction_id'],
            'mobile' => $payment['mobile'],
            'operator' => $payment['operator_name'],
            'amount' => $payment['amount'],
            'method' => $payment['method'],
            'date' => date('Y-m-d H:i:s', strtotime($payment['created_at'])),
            'status' => 'Success'
        ];
    }
}
