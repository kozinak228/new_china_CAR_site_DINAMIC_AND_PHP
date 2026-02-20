<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$page = (int) ($data['page'] ?? 0);
$email = trim($data['email'] ?? '');
$comment = trim($data['comment'] ?? '');

// Если залогинен - берем мыло/логин из сессии
if (isset($_SESSION['id'])) {
    $email = $_SESSION['email'] ?? $_SESSION['login'];
}

if (empty($email) || empty($comment) || $page === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Не все поля заполнены']);
    exit;
}

if (mb_strlen($comment, 'UTF-8') < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Комментарий должен быть длиннее 10 символов']);
    exit;
}

// Защита от XSS (хотя при выводе уже есть nl2br(htmlspecialchars), но сохранять чистое безопаснее)
$email = htmlspecialchars($email);
$comment = htmlspecialchars($comment);

$id = insert('comments', [
    'status' => 1,
    'page' => $page,
    'email' => $email,
    'comment' => $comment
]);

if ($id) {
    // Получаем созданный коммент для возврата
    $newComment = selectOne('comments', ['id' => $id]);

    // Ищем аватарку (если юзер есть)
    $avatarHTML = '<i class="fas fa-user-circle fa-3x text-secondary"></i>';
    if (isset($_SESSION['id'])) {
        $currentUser = selectOne('users', ['id' => $_SESSION['id']]);
        if (!empty($currentUser['avatar'])) {
            $avatarHTML = '<img src="' . BASE_URL . 'assets/images/avatars/' . htmlspecialchars($currentUser['avatar']) . '" width="50" height="50" class="rounded-circle shadow-sm" style="object-fit:cover;" alt="Аватар">';
        }
    }

    // Формируем HTML для вставки
    $html = '<div class="one-comment col-12 d-flex align-items-start mb-3" style="padding: 15px; border-bottom: 1px solid var(--border-color); animation: fadeIn 0.5s;">';
    $html .= '<div class="me-3">' . $avatarHTML . '</div>';
    $html .= '<div class="w-100">';
    $html .= '<div class="mb-1 text-muted small">';
    $html .= '<span class="fw-bold" style="color: var(--primary-color);"><i class="far fa-user"></i> ' . htmlspecialchars($newComment['email']) . '</span> ';
    $html .= '<span class="ms-3"><i class="far fa-calendar-check"></i> ' . $newComment['created_date'] . '</span>';
    $html .= '</div>';
    $html .= '<div class="text mt-2">' . nl2br(htmlspecialchars($newComment['comment'])) . '</div>';
    $html .= '</div></div>';

    echo json_encode([
        'status' => 'success',
        'html' => $html
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка сохранения в БД']);
}
