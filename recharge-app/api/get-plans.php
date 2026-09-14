<?php
/**
 * API: Get Plans (database-driven)
 * GET /api/get-plans.php?operator=jio  or  POST {operator}
 * Returns only ACTIVE plans from admin/data/plans.json.
 */
header('Content-Type: application/json');

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';

$operator = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $operator = strtolower(trim($_GET['operator'] ?? ''));
} else {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $operator = strtolower(trim($input['operator'] ?? ''));
}
if ($operator === '') $operator = 'all';

$allowed = ['jio', 'airtel', 'vi', 'bsnl', 'all'];
if (!in_array($operator, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid operator']);
    exit;
}

$store = new DataStore($basePath . '/admin/data');
$plans = array_values(array_filter(
    $store->getPlans($operator === 'all' ? null : $operator),
    fn($p) => !empty($p['active'])
));
usort($plans, fn($a, $b) => ((float)$a['amount']) <=> ((float)$b['amount']));

$grouped = ['popular' => [], 'unlimited' => [], 'data' => [], 'talktime' => []];
foreach ($plans as $p) {
    $cat = $p['category'] ?? 'popular';
    if (!isset($grouped[$cat])) $grouped[$cat] = [];
    $grouped[$cat][] = $p;
}

echo json_encode(['success' => true, 'data' => [
    'operator' => $operator,
    'plans' => $plans,
    'grouped' => $grouped,
]]);
