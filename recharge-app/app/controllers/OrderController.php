<?php
/**
 * Order Controller
 * Handles order-related operations
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/User.php';

class OrderController {
    /**
     * Create new order
     */
    public static function createOrder($data) {
        $mobile = isset($data['mobile']) ? preg_replace('/[^0-9]/', '', $data['mobile']) : '';
        $planId = isset($data['plan_id']) ? intval($data['plan_id']) : 0;
        $operatorCode = isset($data['operator']) ? $data['operator'] : '';
        
        // Validate
        $errors = [];
        
        if (!User::validateMobile($mobile)) {
            $errors['mobile'] = 'Invalid mobile number';
        }
        
        if ($planId <= 0) {
            $errors['plan_id'] = 'Invalid plan';
        }
        
        if (empty($operatorCode)) {
            $errors['operator'] = 'Operator is required';
        }
        
        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ];
        }
        
        // Get plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            return [
                'status' => 'error',
                'message' => 'Plan not found'
            ];
        }
        
        // Validate operator matches
        if ($plan['operator_code'] !== $operatorCode) {
            return [
                'status' => 'error',
                'message' => 'Plan does not belong to selected operator'
            ];
        }
        
        // Get or create user
        $userId = User::getOrCreate($mobile);
        
        // Create order
        $orderId = Order::create(
            $userId,
            $planId,
            $mobile,
            $plan['operator_id'],
            $plan['amount']
        );
        
        // Create payment
        $paymentId = Payment::create($orderId, $plan['amount'], 'upi');
        
        return [
            'status' => 'success',
            'data' => [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'amount' => $plan['amount'],
                'plan' => $plan,
                'expires_in' => 300
            ]
        ];
    }
    
    /**
     * Get order details
     */
    public static function getOrder($data) {
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        
        if ($orderId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid order ID'
            ];
        }
        
        $order = Order::getById($orderId);
        
        if (!$order) {
            return [
                'status' => 'error',
                'message' => 'Order not found'
            ];
        }
        
        return [
            'status' => 'success',
            'data' => $order
        ];
    }
    
    /**
     * Get user orders
     */
    public static function getUserOrders($data) {
        if (!Auth::isLoggedIn()) {
            return [
                'status' => 'error',
                'message' => 'Please login first'
            ];
        }
        
        $userId = Auth::getUserId();
        $limit = isset($data['limit']) ? intval($data['limit']) : 20;
        $offset = isset($data['offset']) ? intval($data['offset']) : 0;
        
        $orders = Order::getByUserId($userId, $limit, $offset);
        
        return [
            'status' => 'success',
            'data' => [
                'orders' => $orders,
                'count' => count($orders)
            ]
        ];
    }
    
    /**
     * Cancel order
     */
    public static function cancelOrder($data) {
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        
        if ($orderId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid order ID'
            ];
        }
        
        if (!Order::canCancel($orderId)) {
            return [
                'status' => 'error',
                'message' => 'Order cannot be cancelled'
            ];
        }
        
        Order::cancel($orderId);
        
        return [
            'status' => 'success',
            'message' => 'Order cancelled successfully'
        ];
    }
    
    /**
     * Get order receipt
     */
    public static function getReceipt($data) {
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        
        if ($orderId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid order ID'
            ];
        }
        
        $receipt = Order::generateReceipt($orderId);
        
        if (!$receipt) {
            return [
                'status' => 'error',
                'message' => 'Receipt not available'
            ];
        }
        
        return [
            'status' => 'success',
            'data' => $receipt
        ];
    }
}
