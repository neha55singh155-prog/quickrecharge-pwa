<?php
/**
 * Payment Controller
 * Handles payment-related operations
 */

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';

class PaymentController {
    /**
     * Process payment
     */
    public static function processPayment($data) {
        $paymentId = isset($data['payment_id']) ? intval($data['payment_id']) : 0;
        $upiId = isset($data['upi_id']) ? $data['upi_id'] : '';
        
        if ($paymentId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid payment ID'
            ];
        }
        
        $payment = Payment::getById($paymentId);
        
        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment not found'
            ];
        }
        
        if ($payment['status'] !== 'pending') {
            return [
                'status' => 'error',
                'message' => 'Payment already processed'
            ];
        }
        
        // Process UPI payment
        $result = Payment::processUpi($paymentId, $upiId);
        
        if ($result) {
            // Update order status
            $transactionId = Payment::getById($paymentId)['transaction_id'];
            Order::updateStatus($payment['order_id'], 'success', $transactionId);
            
            return [
                'status' => 'success',
                'message' => 'Payment successful',
                'data' => [
                    'payment_id' => $paymentId,
                    'transaction_id' => $transactionId,
                    'order_id' => $payment['order_id']
                ]
            ];
        }
        
        return [
            'status' => 'error',
            'message' => 'Payment failed'
        ];
    }
    
    /**
     * Verify payment
     */
    public static function verifyPayment($data) {
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        $transactionId = isset($data['transaction_id']) ? $data['transaction_id'] : '';
        
        if ($orderId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid order ID'
            ];
        }
        
        // Get payment
        $payment = Payment::getByOrderId($orderId);
        
        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment not found'
            ];
        }
        
        // Verify with gateway (simplified)
        $verified = !empty($transactionId);
        
        if ($verified) {
            Payment::markSuccess($payment['id'], $transactionId);
            Order::updateStatus($orderId, 'success', $transactionId);
            
            // Get order for receipt
            $order = Order::getById($orderId);
            
            return [
                'status' => 'success',
                'message' => 'Payment verified',
                'data' => [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'receipt' => Order::generateReceipt($orderId)
                ]
            ];
        }
        
        Payment::markFailed($payment['id']);
        Order::updateStatus($orderId, 'failed');
        
        return [
            'status' => 'error',
            'message' => 'Payment verification failed'
        ];
    }
    
    /**
     * Handle payment webhook
     */
    public static function handleWebhook($data) {
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        $status = isset($data['status']) ? $data['status'] : '';
        $transactionId = isset($data['transaction_id']) ? $data['transaction_id'] : '';
        
        if ($orderId <= 0 || empty($status)) {
            return [
                'status' => 'error',
                'message' => 'Invalid webhook data'
            ];
        }
        
        // Get payment
        $payment = Payment::getByOrderId($orderId);
        
        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment not found'
            ];
        }
        
        // Update based on status
        switch ($status) {
            case 'success':
                Payment::markSuccess($payment['id'], $transactionId);
                Order::updateStatus($orderId, 'success', $transactionId);
                break;
                
            case 'failed':
                Payment::markFailed($payment['id']);
                Order::updateStatus($orderId, 'failed');
                break;
                
            default:
                // Unknown status, just log it
                break;
        }
        
        return [
            'status' => 'success',
            'message' => 'Webhook processed'
        ];
    }
    
    /**
     * Get payment status
     */
    public static function getPaymentStatus($data) {
        $paymentId = isset($data['payment_id']) ? intval($data['payment_id']) : 0;
        $orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
        
        if ($paymentId > 0) {
            $payment = Payment::getById($paymentId);
        } elseif ($orderId > 0) {
            $payment = Payment::getByOrderId($orderId);
        } else {
            return [
                'status' => 'error',
                'message' => 'Payment ID or Order ID is required'
            ];
        }
        
        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment not found'
            ];
        }
        
        return [
            'status' => 'success',
            'data' => [
                'payment_id' => $payment['id'],
                'status' => $payment['status'],
                'amount' => $payment['amount'],
                'transaction_id' => $payment['transaction_id'],
                'created_at' => $payment['created_at']
            ]
        ];
    }
    
    /**
     * Get user payment history
     */
    public static function getPaymentHistory($data) {
        if (!Auth::isLoggedIn()) {
            return [
                'status' => 'error',
                'message' => 'Please login first'
            ];
        }
        
        $userId = Auth::getUserId();
        $limit = isset($data['limit']) ? intval($data['limit']) : 20;
        
        $payments = Payment::getByUserId($userId, $limit);
        
        return [
            'status' => 'success',
            'data' => [
                'payments' => $payments,
                'count' => count($payments)
            ]
        ];
    }
}
