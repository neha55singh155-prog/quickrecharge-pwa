<?php
/**
 * Operator Model
 * Handles operator-related database operations
 */

class Operator {
    /**
     * Get all active operators
     */
    public static function getAll() {
        return Database::fetchAll(
            "SELECT * FROM operators WHERE is_active = 1 ORDER BY name ASC"
        );
    }
    
    /**
     * Get operator by ID
     */
    public static function getById($id) {
        return Database::fetchOne(
            "SELECT * FROM operators WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Get operator by code
     */
    public static function getByCode($code) {
        return Database::fetchOne(
            "SELECT * FROM operators WHERE code = ?",
            [$code]
        );
    }
    
    /**
     * Detect operator from mobile number
     */
    public static function detectFromMobile($mobile) {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        if (strlen($mobile) !== 10) {
            return null;
        }
        
        $firstTwo = substr($mobile, 0, 2);
        
        // Prefix mapping (simplified)
        $prefixMap = [
            '70' => 'jio',
            '71' => 'jio',
            '72' => 'jio',
            '73' => 'jio',
            '74' => 'jio',
            '75' => 'jio',
            '76' => 'jio',
            '77' => 'jio',
            '78' => 'jio',
            '79' => 'jio',
            '80' => 'airtel',
            '81' => 'airtel',
            '82' => 'airtel',
            '83' => 'airtel',
            '84' => 'airtel',
            '85' => 'airtel',
            '86' => 'airtel',
            '87' => 'airtel',
            '88' => 'airtel',
            '89' => 'airtel',
            '90' => 'vi',
            '91' => 'vi',
            '92' => 'vi',
            '93' => 'vi',
            '94' => 'bsnl',
            '95' => 'bsnl',
            '96' => 'bsnl',
            '97' => 'bsnl',
            '98' => 'jio',
            '99' => 'airtel',
        ];
        
        $code = $prefixMap[$firstTwo] ?? 'jio';
        
        return self::getByCode($code);
    }
    
    /**
     * Get operator plans
     */
    public static function getPlans($operatorId, $category = null) {
        $sql = "SELECT * FROM plans WHERE operator_id = ? AND is_active = 1";
        $params = [$operatorId];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY amount ASC";
        
        return Database::fetchAll($sql, $params);
    }
    
    /**
     * Get operator plan by ID
     */
    public static function getPlanById($planId) {
        return Database::fetchOne(
            "SELECT p.*, o.code as operator_code, o.name as operator_name
             FROM plans p
             JOIN operators o ON p.operator_id = o.id
             WHERE p.id = ? AND p.is_active = 1",
            [$planId]
        );
    }
    
    /**
     * Search plans
     */
    public static function searchPlans($operatorId, $query) {
        return Database::fetchAll(
            "SELECT * FROM plans 
             WHERE operator_id = ? 
             AND is_active = 1 
             AND (description LIKE ? OR data LIKE ? OR calls LIKE ?)
             ORDER BY amount ASC",
            [$operatorId, "%{$query}%", "%{$query}%", "%{$query}%"]
        );
    }
    
    /**
     * Get popular plans
     */
    public static function getPopularPlans($operatorId, $limit = 5) {
        return Database::fetchAll(
            "SELECT * FROM plans 
             WHERE operator_id = ? AND is_active = 1 AND category = 'popular'
             ORDER BY amount ASC
             LIMIT ?",
            [$operatorId, $limit]
        );
    }
}
