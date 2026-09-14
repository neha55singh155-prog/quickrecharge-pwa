<?php
/**
 * Plan Controller
 * Handles plan-related operations
 */

require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Operator.php';

class PlanController {
    /**
     * Get plans by operator
     */
    public static function getPlans($data) {
        $operatorCode = isset($data['operator']) ? $data['operator'] : '';
        
        if (empty($operatorCode)) {
            return [
                'status' => 'error',
                'message' => 'Operator is required'
            ];
        }
        
        // Get operator
        $operator = Operator::getByCode($operatorCode);
        
        if (!$operator) {
            return [
                'status' => 'error',
                'message' => 'Invalid operator'
            ];
        }
        
        // Get plans
        $plans = Plan::getByOperator($operatorCode);
        
        // Group by category
        $grouped = [];
        foreach ($plans as $plan) {
            $grouped[$plan['category']][] = $plan;
        }
        
        return [
            'status' => 'success',
            'data' => [
                'operator' => $operator,
                'plans' => $plans,
                'grouped' => $grouped
            ]
        ];
    }
    
    /**
     * Get plan details
     */
    public static function getPlanDetail($data) {
        $planId = isset($data['plan_id']) ? intval($data['plan_id']) : 0;
        
        if ($planId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid plan ID'
            ];
        }
        
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            return [
                'status' => 'error',
                'message' => 'Plan not found'
            ];
        }
        
        return [
            'status' => 'success',
            'data' => $plan
        ];
    }
    
    /**
     * Search plans
     */
    public static function searchPlans($data) {
        $query = isset($data['query']) ? $data['query'] : '';
        $operatorCode = isset($data['operator']) ? $data['operator'] : null;
        
        if (empty($query)) {
            return [
                'status' => 'error',
                'message' => 'Search query is required'
            ];
        }
        
        if ($operatorCode) {
            $plans = Operator::searchPlans(
                Operator::getByCode($operatorCode)['id'],
                $query
            );
        } else {
            $plans = Plan::search($query);
        }
        
        return [
            'status' => 'success',
            'data' => [
                'plans' => $plans,
                'count' => count($plans)
            ]
        ];
    }
    
    /**
     * Get popular plans
     */
    public static function getPopularPlans($data) {
        $operatorCode = isset($data['operator']) ? $data['operator'] : null;
        $limit = isset($data['limit']) ? intval($data['limit']) : 5;
        
        if ($operatorCode) {
            $plans = Plan::getPopular($operatorCode, $limit);
        } else {
            // Get from all operators
            $operators = Operator::getAll();
            $plans = [];
            
            foreach ($operators as $operator) {
                $operatorPlans = Plan::getPopular($operator['code'], $limit);
                $plans = array_merge($plans, $operatorPlans);
            }
        }
        
        return [
            'status' => 'success',
            'data' => [
                'plans' => $plans
            ]
        ];
    }
    
    /**
     * Get plan categories
     */
    public static function getCategories($data) {
        $operatorCode = isset($data['operator']) ? $data['operator'] : null;
        
        if (!$operatorCode) {
            return [
                'status' => 'error',
                'message' => 'Operator is required'
            ];
        }
        
        $categories = Plan::getCategories($operatorCode);
        
        return [
            'status' => 'success',
            'data' => [
                'categories' => $categories
            ]
        ];
    }
}
