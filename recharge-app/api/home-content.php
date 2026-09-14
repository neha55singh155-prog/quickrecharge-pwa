<?php
header('Content-Type: application/json');

$basePath = dirname(__DIR__);
require_once $basePath . '/admin/data/store.php';

$store = new DataStore($basePath . '/admin/data');
$home = $store->getHomeContent();

echo json_encode($home, JSON_UNESCAPED_UNICODE);
