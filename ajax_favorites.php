<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['car_id'])) {
    $car_id = intval($data['car_id']);
    $user_id = $_SESSION['id'];

    // Проверяем, есть ли уже в закладках
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE id_user = ? AND id_car = ?");
    $stmt->execute([$user_id, $car_id]);
    $fav = $stmt->fetch();

    if ($fav) {
        // Удаляем из закладок
        $del = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
        $del->execute([$fav['id']]);
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // Добавляем в закладки
        $ins = $pdo->prepare("INSERT INTO favorites (id_user, id_car) VALUES (?, ?)");
        $ins->execute([$user_id, $car_id]);
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
