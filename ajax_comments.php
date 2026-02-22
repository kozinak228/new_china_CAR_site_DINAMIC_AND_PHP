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

$rating = (int) ($data['rating'] ?? 5);
if ($rating < 1)
    $rating = 1;
if ($rating > 5)
    $rating = 5;

// Защита от XSS (хотя при выводе уже есть nl2br(htmlspecialchars), но сохранять чистое безопаснее)
$email = htmlspecialchars($email);
$comment = htmlspecialchars($comment);

$id = insert('comments', [
    'status' => 1,
    'page' => $page,
    'email' => $email,
    'comment' => $comment,
    'rating' => $rating
]);

if ($id) {
    // Получаем созданный коммент для возврата
    $newComment = selectOne('comments', ['id' => $id]);

    // Ищем аватарку (если юзер есть)
    $avatarHTML = '<i class="fas fa-user-circle fa-2x text-slate-400"></i>';
    if (isset($_SESSION['id'])) {
        $currentUser = selectOne('users', ['id' => $_SESSION['id']]);
        if (!empty($currentUser['avatar'])) {
            $avatarHTML = '<img src="' . BASE_URL . 'assets/images/avatars/' . htmlspecialchars($currentUser['avatar']) . '" class="w-10 h-10 rounded-full object-cover shadow-sm" alt="Аватар">';
        }
    }

    $starsHTML = '<div class="flex gap-0.5 text-primary">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $starsHTML .= '<span class="material-icons text-xl font-bold">star</span>';
        } else {
            $starsHTML .= '<span class="material-icons text-xl">star_border</span>';
        }
    }
    $starsHTML .= '</div>';

    $menuHTML = '';
    $currentUserEmail = $_SESSION['email'] ?? $_SESSION['login'] ?? '';
    if (isset($_SESSION['id'])) {
        $menuHTML = '
        <div class="flex items-center gap-1 ml-2 border-l border-slate-200 dark:border-white/10 pl-3">
            <button type="button" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-all edit-btn" title="Редактировать">
                <span class="material-icons text-[16px]">edit</span>
            </button>
            <button type="button" class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all delete-btn" title="Удалить">
                <span class="material-icons text-[16px]">delete</span>
            </button>
        </div>';
    }

    $formattedDate = date('M d, Y', strtotime($newComment['created_date']));
    $html = '<div class="one-comment p-6 glass rounded-[2rem] space-y-4 hover:shadow-lg transition-all shadow-sm animate-fade-in-up" data-id="' . $id . '">';
    $html .= '<div class="flex items-center justify-between border-b border-slate-200/50 dark:border-white/5 pb-4 relative">';
    $html .= '<div class="flex items-center gap-3">';
    $html .= $avatarHTML;
    $html .= '<div>';
    $html .= '<p class="text-sm font-bold text-slate-900 dark:text-white">' . htmlspecialchars($newComment['email']) . '</p>';
    $html .= '<p class="text-[10px] text-slate-400 uppercase tracking-widest">' . $formattedDate . '</p>';
    $html .= '</div></div>';
    $html .= '<div class="flex flex-col items-end gap-2"><div class="flex items-center gap-2 text-primary"><div class="flex gap-0.5">' . $starsHTML . '</div>' . $menuHTML . '</div></div>';
    $html .= '</div>';
    $html .= '<div class="comment-body relative"><div class="comment-content text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">' . htmlspecialchars($newComment['comment']) . '</div>';
    $html .= '<div class="edit-ui hidden w-full space-y-3 mt-2">
                <textarea class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-[14px] text-slate-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-y min-h-[80px] transition-all" placeholder="Отредактируйте ваш комментарий..."></textarea>
                <div class="text-[11px] text-right font-medium pr-1 mt-1"><span class="edit-char-count text-red-500">0</span><span class="text-slate-400">/1000 MAX</span></div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="cancel-edit-btn px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors">Отмена</button>
                    <button type="button" class="save-edit-btn px-5 py-2 text-xs font-bold bg-primary hover:bg-primary/90 text-white rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center justify-center min-w-[100px]">
                        <span class="save-text">Сохранить</span>
                        <div class="save-loader hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </button>
                </div>
              </div></div>';
    $html .= '<div class="flex items-center gap-4 pt-2">
                <button class="flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-primary transition-colors like-btn" data-id="' . $id . '">
                    <span class="material-icons text-sm">thumb_up_off_alt</span>
                    <span class="like-count">0</span>
                </button>
              </div>';
    $html .= '</div>';

    echo json_encode([
        'status' => 'success',
        'html' => $html
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка сохранения в БД']);
}
