<?php
require_once __DIR__ . '/../../app/Model/Article.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? max(1,intval($_GET['limit'])) : 10;

if ($q === '') {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

try {
    $results = Article::searchByTitle($q, $limit);
    echo json_encode(['success' => true, 'data' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
