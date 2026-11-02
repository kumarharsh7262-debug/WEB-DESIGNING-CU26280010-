<?php
/** Task 20: JSON endpoint for live search suggestions (called with fetch()) */
require_once __DIR__ . '/includes/init.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q'] ?? '');
if ($q === '' || mb_strlen($q) > 100) {
    echo json_encode([]);
    exit;
}

$like = '%' . addcslashes($q, '%_\\') . '%';
$stmt = db_query($conn, 'SELECT id, name, category FROM products WHERE name LIKE ? ORDER BY name LIMIT 8', 's', [$like]);

echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
