<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$car_id = isset($data['car_id']) ? (int) $data['car_id'] : 0;
$action = isset($data['action']) ? trim($data['action']) : ''; // 'add' or 'remove'

if ($car_id === 0 || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    exit;
}

// Ensure the compare array exists in session
if (!isset($_SESSION['compare'])) {
    $_SESSION['compare'] = [];
}

$is_added = false;

if ($action === 'add') {
    if (!in_array($car_id, $_SESSION['compare'])) {
        // Limit to 4 cars for comparison to avoid breaking UI
        if (count($_SESSION['compare']) >= 4) {
            echo json_encode(['status' => 'error', 'message' => 'Maximum 4 cars allowed for comparison']);
            exit;
        }
        $_SESSION['compare'][] = $car_id;
        $is_added = true;
    }
} elseif ($action === 'remove') {
    $index = array_search($car_id, $_SESSION['compare']);
    if ($index !== false) {
        unset($_SESSION['compare'][$index]);
        // Re-index array
        $_SESSION['compare'] = array_values($_SESSION['compare']);
    }
}

$count = count($_SESSION['compare']);

echo json_encode([
    'status' => 'success',
    'count' => $count,
    'is_added' => $is_added,
    'compare_list' => $_SESSION['compare']
]);
