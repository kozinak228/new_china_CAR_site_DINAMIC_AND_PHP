<?php
// контроллер
include_once SITE_ROOT . "/app/database/db.php";
$commentsForAdm = selectAll('comments');

$page = $_GET['id'] ?? $_GET['post'] ?? '';
$email = '';
$comment = '';
$errMsg = [];
$status = 1; // все комментарии публикуются сразу
$comments = [];


// Код для формы создания комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goComment'])) {

    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    // Если пользователь авторизован, берём email/username из сессии
    if (isset($_SESSION['id']) && $email === '') {
        $email = $_SESSION['login'] ?? '';
    }

    if ($email === '' || $comment === '') {
        array_push($errMsg, "Не все поля заполнены!");
    } elseif (mb_strlen($comment, 'UTF8') < 10) {
        array_push($errMsg, "Комментарий должен быть длинее 10 символов");
    } else {
        // Если админ — авто-публикация
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
            $status = 1;
        }

        $comment = [
            'status' => $status,
            'page' => $page,
            'email' => $email,
            'comment' => $comment
        ];

        $comment = insert('comments', $comment);
        $comments = selectAll('comments', ['page' => $page, 'status' => 1]);

    }
} else {
    $email = '';
    $comment = '';
    $comments = selectAll('comments', ['page' => $page, 'status' => 1]);

}
// Удаление комментария
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    delete('comments', $id);
    header('location: ' . BASE_URL . 'admin/comments/index.php');
}

// Статус опубликовать или снять с публикации
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
    $id = $_GET['pub_id'];
    $publish = $_GET['publish'];

    $postId = update('comments', $id, ['status' => $publish]);

    header('location: ' . BASE_URL . 'admin/comments/index.php');
    exit();
}


// АПДЕЙТ КОММЕНТАРИЯ (только в админке)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])) {
    $oneComment = selectOne('comments', ['id' => $_GET['edit_id']]);
    if ($oneComment) {
        $id = $oneComment['id'];
        $email = $oneComment['email'];
        $text1 = $oneComment['comment'];
        $pub = $oneComment['status'];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    $id = $_POST['id'];
    $text = trim($_POST['content']);
    $publish = isset($_POST['publish']) ? 1 : 0;

    if ($text === '') {
        array_push($errMsg, "Комментарий не имеет содержимого текста");
    } elseif (mb_strlen($text, 'UTF8') < 50) {
        array_push($errMsg, "Количество символов внутри комментария меньше 50");
    } else {
        $com = [
            'comment' => $text,
            'status' => $publish
        ];

        $comment = update('comments', $id, $com);
        header('location: ' . BASE_URL . 'admin/comments/index.php');
    }
} else {
    if (isset($_POST['content'])) {
        $text = trim($_POST['content']);
    } else {
        $text = '';
    }

    if (isset($_POST['publish'])) {
        $publish = 1;
    } else {
        $publish = 0;
    }
}