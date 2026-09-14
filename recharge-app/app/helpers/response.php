<?php
/**
 * API Response Helper
 * Standard JSON responses
 */

class Response {
    /**
     * Send success response
     */
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Send error response
     */
    public static function error($message = 'Error', $code = 400, $data = null) {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Send validation error
     */
    public static function validation($errors) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Get JSON input from request
     */
    public static function getInput() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            self::error('Invalid JSON input', 400);
        }
        
        return $data;
    }
    
    /**
     * Get input field with validation
     */
    public static function getField($field, $required = true, $default = null) {
        $input = self::getInput();
        
        if ($required && (!isset($input[$field]) || $input[$field] === '')) {
            self::validation([$field => "{$field} is required"]);
        }
        
        return $input[$field] ?? $default;
    }
}
