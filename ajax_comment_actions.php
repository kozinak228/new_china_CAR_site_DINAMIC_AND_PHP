<?php
include "path.php";
include "app/database/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$comment_id = (int) ($input['id'] ?? 0);

if (!$comment_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing comment ID']);
    exit;
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Вы должны войти в систему']);
    exit;
}

$user_id = $_SESSION['id'];
$user_email = $_SESSION['email'] ?? $_SESSION['login'];

// Fetch comment to check ownership
$comment = selectOne('comments', ['id' => $comment_id]);
if (!$comment) {
    echo json_encode(['status' => 'error', 'message' => 'Комментарий не найден']);
    exit;
}

// В БД email сохраняется через htmlspecialchars(), поэтому декодируем перед строгим сравнением
$is_author = (htmlspecialchars_decode($comment['email']) === $user_email);

if ($action === 'like') {
    $existing_like = selectOne('comment_likes', ['user_id' => $user_id, 'comment_id' => $comment_id]);

    if ($existing_like) {
        // Toggle off: remove like record and decrement count
        delete('comment_likes', $existing_like['id']);
        $new_likes = max(0, $comment['likes'] - 1);
        update('comments', $comment_id, ['likes' => $new_likes]);
        echo json_encode(['status' => 'success', 'likes' => $new_likes, 'liked' => false]);
    } else {
        // Toggle on: add like record and increment count
        insert('comment_likes', ['user_id' => $user_id, 'comment_id' => $comment_id]);
        $new_likes = $comment['likes'] + 1;
        update('comments', $comment_id, ['likes' => $new_likes]);
        echo json_encode(['status' => 'success', 'likes' => $new_likes, 'liked' => true]);
    }
    exit;
}

if ($action === 'delete') {
    if (!$is_author && !isset($_SESSION['admin'])) {
        echo json_encode(['status' => 'error', 'message' => 'Нет прав для удаления']);
        exit;
    }
    // Instead of deleting, we can set status to 0 or delete from DB
    delete('comments', $comment_id);
    echo json_encode(['status' => 'success', 'message' => 'Комментарий удален']);
    exit;
}

if ($action === 'edit') {
    if (!$is_author) {
        echo json_encode(['status' => 'error', 'message' => 'Нет прав для редактирования']);
        exit;
    }
    $new_text = trim($input['comment'] ?? '');
    if (strlen($new_text) < 10) {
        echo json_encode(['status' => 'error', 'message' => 'Минимальная длина — 10 символов']);
        exit;
    }
    update('comments', $comment_id, ['comment' => $new_text, 'status' => 1]); // Status 1 to ensure it's visible
    echo json_encode(['status' => 'success', 'comment' => nl2br(htmlspecialchars($new_text))]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
?>