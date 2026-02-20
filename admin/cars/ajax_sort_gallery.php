<?php
include "../../path.php";
include_once SITE_ROOT . "/app/database/db.php";

session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['order']) && is_array($data['order'])) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            $sql = "UPDATE car_images SET sort_order = :sort_order WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            foreach ($data['order'] as $item) {
                if (isset($item['id']) && isset($item['sort_order'])) {
                    $stmt->execute([
                        ':sort_order' => (int) $item['sort_order'],
                        ':id' => (int) $item['id']
                    ]);
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не разрешен']);
}
