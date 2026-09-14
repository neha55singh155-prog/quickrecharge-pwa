<?php
/**
 * Plan Model
 * Handles plan-related database operations
 */

class Plan {
    /**
     * Get plan by ID
     */
    public static function getById($id) {
        return Database::fetchOne(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE p.id = ? AND p.is_active = 1",
            [$id]
        );
    }
    
    /**
     * Get all plans
     */
    public static function getAll() {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE p.is_active = 1
             ORDER BY p.amount ASC"
        );
    }
    
    /**
     * Get plans by operator
     */
    public static function getByOperator($operatorCode) {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE o.code = ? AND p.is_active = 1
             ORDER BY p.amount ASC",
            [$operatorCode]
        );
    }
    
    /**
     * Get plans by category
     */
    public static function getByCategory($operatorCode, $category) {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE o.code = ? AND p.category = ? AND p.is_active = 1
             ORDER BY p.amount ASC",
            [$operatorCode, $category]
        );
    }
    
    /**
     * Search plans
     */
    public static function search($query) {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE p.is_active = 1
             AND (p.description LIKE ? OR p.data LIKE ? OR p.calls LIKE ?)
             ORDER BY p.amount ASC",
            ["%{$query}%", "%{$query}%", "%{$query}%"]
        );
    }
    
    /**
     * Get plans in price range
     */
    public static function getByPriceRange($minAmount, $maxAmount) {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE p.is_active = 1 AND p.amount BETWEEN ? AND ?
             ORDER BY p.amount ASC",
            [$minAmount, $maxAmount]
        );
    }
    
    /**
     * Get popular plans
     */
    public static function getPopular($operatorCode, $limit = 5) {
        return Database::fetchAll(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE o.code = ? AND p.category = 'popular' AND p.is_active = 1
             ORDER BY p.amount ASC
             LIMIT ?",
            [$operatorCode, $limit]
        );
    }
    
    /**
     * Get plan categories
     */
    public static function getCategories($operatorCode) {
        return Database::fetchAll(
            "SELECT DISTINCT category, COUNT(*) as count
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE o.code = ? AND p.is_active = 1
             GROUP BY category
             ORDER BY count DESC",
            [$operatorCode]
        );
    }
    
    /**
     * Validate plan
     */
    public static function validate($planId, $operatorCode) {
        $plan = self::getById($planId);
        
        if (!$plan) {
            return false;
        }
        
        if ($plan['operator_code'] !== $operatorCode) {
            return false;
        }
        
        return true;
    }
}
