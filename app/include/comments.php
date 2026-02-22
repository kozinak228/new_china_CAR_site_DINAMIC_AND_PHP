<div class="col-12 comments pt-8" id="comments">
    <?php
    // Fetch all likes by the current user for highlight
    $userLikes = [];
    if (isset($_SESSION['id'])) {
        $likesData = selectAll('comment_likes', ['user_id' => $_SESSION['id']]);
        foreach ($likesData as $l) {
            $userLikes[] = $l['comment_id'];
        }
    }
    ?>
    <div class="glass p-8 rounded-[2rem] shadow-sm animate-fade-in-up" style="animation-delay: 0.3s;">
        <h2 class="text-2xl font-bold mb-6 text-slate-800 dark:text-white flex items-center gap-2">
            <span class="material-icons text-primary">rate_review</span> Оставить отзыв
        </h2>

        <?php if (!empty($errMsg)): ?>
            <div
                class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 dark:bg-slate-900 dark:text-red-400 border border-red-200 dark:border-red-800/30">
                <?php foreach ($errMsg as $e): ?>
                    <p class="mb-0"><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div id="ajaxErrorBlock"
            class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 dark:bg-slate-900 dark:text-red-400 border border-red-200 dark:border-red-800/30"
            style="display: none;"></div>

        <form id="ajaxCommentForm" action="<?= BASE_URL . "single.php?id=$page" ?>" method="post" class="space-y-6">
            <input type="hidden" name="page" id="commentPageId" value="<?= (int) $page ?>">

            <?php if (isset($_SESSION['id'])): ?>
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Вы вошли как: <strong
                        class="text-slate-900 dark:text-white"><?= htmlspecialchars($_SESSION['login']) ?></strong>
                    <input type="hidden" name="email" id="commentEmail"
                        value="<?= htmlspecialchars($_SESSION['email'] ?? $_SESSION['login']) ?>">
                </div>

                <div class="relative group mt-6">
                    <textarea name="comment"
                        class="peer w-full bg-white/10 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-xl p-4 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder-transparent shadow-inner backdrop-blur-md"
                        id="commentText" rows="4" placeholder="..." required></textarea>
                    <label
                        class="absolute left-4 top-4 text-slate-500 dark:text-slate-400 pointer-events-none transition-all peer-focus:-top-3 peer-focus:left-2 peer-focus:text-xs peer-focus:text-primary peer-focus:bg-white/80 dark:peer-focus:bg-slate-900/80 peer-focus:backdrop-blur-sm peer-focus:px-2 peer-focus:rounded peer-[:not(:placeholder-shown)]:-top-3 peer-[:not(:placeholder-shown)]:left-2 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-primary peer-[:not(:placeholder-shown)]:bg-white/80 dark:peer-[:not(:placeholder-shown)]:bg-slate-900/80 peer-[:not(:placeholder-shown)]:backdrop-blur-sm peer-[:not(:placeholder-shown)]:px-2 peer-[:not(:placeholder-shown)]:rounded"
                        for="commentText">Напишите ваш комментарий...</label>
                </div>

                <div class="flex flex-col gap-2 mt-4">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Ваша оценка:</label>
                    <div class="flex gap-1 rating-selector">
                        <input type="hidden" name="rating" id="ratingValue" value="5">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="material-icons cursor-pointer star-item text-primary transition-all hover:scale-110"
                                data-value="<?= $i ?>">star</span>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="text-xs font-medium mt-1">
                    <span id="charCount" class="text-primary">0</span> <span class="text-slate-400">/ мин. 10
                        символов</span>
                </div>

                <button type="submit" name="goComment"
                    class="w-full bg-primary hover:bg-red-600 text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2 group shadow-[0_0_20px_rgba(225,29,72,0.3)] border-none cursor-pointer">
                    Отправить отзыв
                    <span class="material-icons group-hover:translate-x-1 transition-transform">send</span>
                </button>
            <?php else: ?>
                <div class="relative group opacity-60 mt-6">
                    <textarea name="comment"
                        class="w-full bg-white/50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 rounded-xl p-4 text-slate-500 cursor-not-allowed outline-none backdrop-blur-sm"
                        id="commentText" rows="4" disabled placeholder="Напишите ваш комментарий..."></textarea>
                </div>

                <div class="text-center mt-4 text-slate-600 dark:text-slate-400 font-medium" id="authMessage">
                    Сначала <a href="<?= BASE_URL ?>log.php"
                        class="text-primary hover:underline font-bold">авторизуйтесь</a> или
                    <a href="<?= BASE_URL ?>reg.php" class="text-primary hover:underline font-bold">зарегистрируйтесь</a>,
                    чтобы
                    оставить отзывы.
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php
    // Загружаем комментарии для этой машины с аватарками пользователей
    global $pdo;
    $sql = "SELECT c.*, u.avatar 
            FROM comments c 
            LEFT JOIN users u ON c.email = u.email OR c.email = u.username 
            WHERE c.page = ? AND c.status = 1 
            ORDER BY c.likes DESC, c.id DESC";
    $query = $pdo->prepare($sql);
    $query->execute([$page]);
    $comments = $query->fetchAll();
    $currentUserEmail = $_SESSION['email'] ?? $_SESSION['login'] ?? '';
    ?>

    <?php if (!empty($comments)): ?>
        <div class="all-comments mt-10 space-y-6 animate-fade-in-up" style="animation-delay: 0.4s;">
            <h3 class="text-2xl font-bold flex items-center gap-2 text-slate-900 dark:text-white mb-6">
                <span class="w-8 h-1 bg-primary rounded-full"></span> Отзывы об автомобиле
            </h3>
            <div class="grid grid-cols-1 gap-4" id="commentsGrid">
                <?php foreach ($comments as $index => $c): ?>
                    <div class="one-comment p-6 glass rounded-[2rem] space-y-4 hover:shadow-lg transition-all shadow-sm <?= $index >= 4 ? 'hidden' : '' ?>"
                        data-index="<?= $index ?>" data-id="<?= $c['id'] ?>">
                        <div
                            class="flex items-center justify-between border-b border-slate-200/50 dark:border-white/5 pb-4 relative">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($c['avatar'])): ?>
                                    <img src="<?= BASE_URL ?>assets/images/avatars/<?= htmlspecialchars($c['avatar']) ?>"
                                        class="w-10 h-10 rounded-full object-cover shadow-sm" alt="Аватар">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x text-slate-400"></i>
                                <?php endif; ?>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                        <?= htmlspecialchars($c['email']) ?>
                                    </p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">
                                        <?= date('M d, Y', strtotime($c['created_date'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2 text-primary">
                                    <div class="flex gap-0.5">
                                        <?php
                                        $rating = isset($c['rating']) ? (int) $c['rating'] : 5;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<span class="material-icons text-xl font-bold">star</span>';
                                            } else {
                                                echo '<span class="material-icons text-xl">star_border</span>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <?php
                                    $isOwner = (isset($_SESSION['id']) && htmlspecialchars_decode($c['email']) === $currentUserEmail);
                                    $isAdmin = (isset($_SESSION['admin']) && $_SESSION['admin'] == 1);
                                    if ($isOwner || $isAdmin):
                                        ?>
                                        <div
                                            class="flex items-center gap-1 ml-2 border-l border-slate-200 dark:border-white/10 pl-3">
                                            <button type="button"
                                                class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-all edit-btn"
                                                title="Редактировать">
                                                <span class="material-icons text-[16px]">edit</span>
                                            </button>
                                            <button type="button"
                                                class="w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all delete-btn"
                                                title="Удалить">
                                                <span class="material-icons text-[16px]">delete</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="comment-body relative">
                            <div class="comment-content text-slate-600 dark:text-slate-300 leading-relaxed">
                                <?= nl2br(trim(htmlspecialchars($c['comment']))) ?></div>
                            <div class="edit-ui hidden w-full space-y-3 mt-2">
                                <textarea
                                    class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-[14px] text-slate-700 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-y min-h-[80px] transition-all"
                                    placeholder="Отредактируйте ваш комментарий..."></textarea>
                                <div class="text-[11px] text-right font-medium pr-1 mt-1"><span
                                        class="edit-char-count text-red-500">0</span><span class="text-slate-400">/1000
                                        MAX</span></div>
                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                        class="cancel-edit-btn px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-colors">Отмена</button>
                                    <button type="button"
                                        class="save-edit-btn px-5 py-2 text-xs font-bold bg-primary hover:bg-primary/90 text-white rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center justify-center min-w-[100px]">
                                        <span class="save-text">Сохранить</span>
                                        <div
                                            class="save-loader hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin">
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 pt-2">
                            <?php $isLiked = in_array($c['id'], $userLikes); ?>
                            <button
                                class="flex items-center gap-1.5 text-xs font-bold transition-colors like-btn <?= $isLiked ? 'text-primary' : 'text-slate-500 hover:text-primary' ?>"
                                data-id="<?= $c['id'] ?>">
                                <span class="material-icons text-sm"><?= $isLiked ? 'thumb_up' : 'thumb_up_off_alt' ?></span>
                                <span class="like-count"><?= (int) ($c['likes'] ?? 0) ?></span>
                            </button>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <?php if (count($comments) > 4): ?>
                <div class="flex justify-center mt-8">
                    <button id="loadMoreBtn"
                        class="glass px-8 py-3 rounded-full text-slate-600 dark:text-slate-300 font-bold hover:bg-primary hover:text-white transition-all shadow-sm flex items-center gap-2 group cursor-pointer border-none">
                        Показать больше
                        <span class="material-icons text-sm group-hover:rotate-180 transition-transform">expand_more</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commentText = document.getElementById('commentText');
        const charCount = document.getElementById('charCount');
        const commentForm = document.getElementById('ajaxCommentForm');
        const errorBlock = document.getElementById('ajaxErrorBlock');

        // Счётчик символов
        if (commentText && charCount) {
            // Initial check if there's already some text (e.g. on reload)
            charCount.textContent = commentText.value.length;

            commentText.addEventListener('input', function () {
                const len = this.value.length;
                charCount.textContent = len;
                charCount.style.color = len >= 10 ? '#28a745' : '#e11d48';
            });
        }

        // AJAX отправка
        if (commentForm) {
            commentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                errorBlock.style.display = 'none';
                errorBlock.innerHTML = '';

                const commentVal = commentText.value.trim();
                if (commentVal.length < 10) {
                    errorBlock.innerHTML = '<p class="mb-0">Минимальная длина комментария — 10 символов.</p>';
                    errorBlock.style.display = 'block';
                    return;
                }

                const pageId = document.getElementById('commentPageId').value;
                const emailInput = document.getElementById('commentEmail') ? document.getElementById('commentEmail').value : '';

                fetch('<?= BASE_URL ?>ajax_comments.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        page: pageId,
                        email: emailInput,
                        comment: commentVal,
                        rating: document.getElementById('ratingValue').value
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            commentText.value = '';
                            if (charCount) {
                                charCount.textContent = '0';
                                charCount.style.color = '#e11d48';
                            }

                            let commentsContainer = document.querySelector('.all-comments');
                            if (!commentsContainer) {
                                commentsContainer = document.createElement('div');
                                commentsContainer.className = 'all-comments mt-10 space-y-6 animate-fade-in-up';
                                commentsContainer.innerHTML = '<h3 class="text-2xl font-bold flex items-center gap-2 text-slate-900 dark:text-white mb-6"><span class="w-8 h-1 bg-primary rounded-full"></span> Отзывы об автомобиле</h3><div class="grid grid-cols-1 gap-4" id="commentsGrid"></div>';
                                document.querySelector('.comments > div').appendChild(commentsContainer);
                            }

                            let grid = document.getElementById('commentsGrid') || commentsContainer.querySelector('.grid') || commentsContainer;
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const newCommentNode = tempDiv.firstChild;

                            if (grid.firstChild) {
                                grid.insertBefore(newCommentNode, grid.firstChild);
                            } else {
                                grid.appendChild(newCommentNode);
                            }

                            newCommentNode.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            errorBlock.innerHTML = '<p class="mb-0">' + data.message + '</p>';
                            errorBlock.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorBlock.innerHTML = '<p class="mb-0">Ошибка сети или сервера.</p>';
                        errorBlock.style.display = 'block';
                    });
            });
        }

        // Логика выбора звезд
        const stars = document.querySelectorAll('.star-item');
        const ratingInput = document.getElementById('ratingValue');

        if (stars.length > 0 && ratingInput) {
            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const val = this.dataset.value;
                    ratingInput.value = val;
                    updateStars(val);
                });

                star.addEventListener('mouseover', function () {
                    updateStars(this.dataset.value);
                });

                star.addEventListener('mouseout', function () {
                    updateStars(ratingInput.value);
                });
            });

            function updateStars(val) {
                stars.forEach(s => {
                    if (s.dataset.value <= val) {
                        s.textContent = 'star';
                    } else {
                        s.textContent = 'star_border';
                    }
                });
            }
        }

        // Логика "Показать больше"
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                const hiddenComments = document.querySelectorAll('.one-comment.hidden');
                for (let i = 0; i < 4 && i < hiddenComments.length; i++) {
                    hiddenComments[i].classList.remove('hidden');
                    hiddenComments[i].classList.add('animate-fade-in-up');
                }

                if (document.querySelectorAll('.one-comment.hidden').length === 0) {
                    loadMoreBtn.parentElement.style.display = 'none';
                }
            });
        }

        // --- Новая логика (Лайки, Меню, Удаление/Редактирование) ---
        const commentsGrid = document.getElementById('commentsGrid');

        // Клик по сетке комментариев (делегирование)
        if (commentsGrid) {
            commentsGrid.addEventListener('click', function (e) {
                const target = e.target;

                // Три точки (Меню) - Удалено в новой версии, но оставляем заглушку на случай старого кэша
                const menuBtn = target.closest('.comment-menu-btn');
                if (menuBtn) {
                    const menu = menuBtn.nextElementSibling;
                    if (menu) {
                        document.querySelectorAll('.comment-menu').forEach(m => {
                            if (m !== menu) m.classList.add('hidden');
                        });
                        menu.classList.toggle('hidden');
                    }
                    return;
                }

                // Лайк
                const likeBtn = target.closest('.like-btn');
                if (likeBtn) {
                    const id = likeBtn.dataset.id;
                    fetch('<?= BASE_URL ?>ajax_comment_actions.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'like', id: id })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                likeBtn.querySelector('.like-count').textContent = data.likes;
                                const icon = likeBtn.querySelector('.material-icons');
                                if (data.liked) {
                                    likeBtn.classList.remove('text-slate-500', 'hover:text-primary');
                                    likeBtn.classList.add('text-primary');
                                    icon.textContent = 'thumb_up';
                                } else {
                                    likeBtn.classList.add('text-slate-500', 'hover:text-primary');
                                    likeBtn.classList.remove('text-primary');
                                    icon.textContent = 'thumb_up_off_alt';
                                }
                            }
                        });
                    return;
                }

                // Удаление
                const deleteBtn = target.closest('.delete-btn');
                if (deleteBtn) {
                    e.preventDefault();
                    if (confirm('Вы уверены, что хотите удалить этот комментарий?')) {
                        const commentBlock = deleteBtn.closest('.one-comment');
                        const id = commentBlock.dataset.id;
                        fetch('<?= BASE_URL ?>ajax_comment_actions.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'delete', id: id })
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    commentBlock.style.opacity = '0';
                                    setTimeout(() => commentBlock.remove(), 300);
                                } else {
                                    alert(data.message);
                                }
                            });
                    }
                    return;
                }

                // Редактирование (Показать UI)
                const editBtn = target.closest('.edit-btn');
                if (editBtn) {
                    e.preventDefault();
                    const commentBlock = editBtn.closest('.one-comment');
                    const contentEl = commentBlock.querySelector('.comment-content');
                    const editUi = commentBlock.querySelector('.edit-ui');
                    const textarea = editUi.querySelector('textarea');

                    // Закрываем потенциальное старое меню
                    const oldMenu = commentBlock.querySelector('.comment-menu');
                    if (oldMenu) oldMenu.classList.add('hidden');

                    // Скрываем текст, показываем форму
                    contentEl.classList.add('hidden');
                    editUi.classList.remove('hidden');

                    // Берем оригинальный текст до редактирования и убираем лишние пробелы по краям
                    textarea.value = contentEl.innerHTML.replace(/<br\s*[\/]?>/gi, "\n").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&').trim();
                    textarea.focus();

                    // Автоматическая высота и подсчет символов
                    const charCountSp = editUi.querySelector('.edit-char-count');

                    // Инициализация при открытии (сразу считаем длину старого текста)
                    const initLen = textarea.value.length;
                    if (charCountSp) {
                        charCountSp.textContent = initLen;
                        charCountSp.style.color = initLen >= 10 ? '#28a745' : '#e11d48';
                    }

                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight) + 'px';

                    textarea.addEventListener('input', function () {
                        this.style.height = 'auto';
                        this.style.height = (this.scrollHeight) + 'px';

                        if (charCountSp) {
                            const currentLen = this.value.length;
                            charCountSp.textContent = currentLen;
                            charCountSp.style.color = currentLen >= 10 ? '#28a745' : '#e11d48';
                        }
                    });

                    return;
                }

                // Отмена редактирования
                const cancelBtn = target.closest('.cancel-edit-btn');
                if (cancelBtn) {
                    const commentBlock = cancelBtn.closest('.one-comment');
                    commentBlock.querySelector('.comment-content').classList.remove('hidden');
                    commentBlock.querySelector('.edit-ui').classList.add('hidden');
                    return;
                }

                // Сохранение редактирования
                const saveBtn = target.closest('.save-edit-btn');
                if (saveBtn) {
                    const commentBlock = saveBtn.closest('.one-comment');
                    const contentEl = commentBlock.querySelector('.comment-content');
                    const editUi = commentBlock.querySelector('.edit-ui');
                    const textarea = editUi.querySelector('textarea');
                    const newText = textarea.value.trim();
                    const id = commentBlock.dataset.id;

                    const saveText = saveBtn.querySelector('.save-text');
                    const saveLoader = saveBtn.querySelector('.save-loader');

                    if (newText.length < 10) {
                        alert('Минимальная длина комментария — 10 символов.');
                        return;
                    }

                    // Анимация загрузки
                    saveText.classList.add('hidden');
                    saveLoader.classList.remove('hidden');
                    saveBtn.disabled = true;

                    fetch('<?= BASE_URL ?>ajax_comment_actions.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'edit', id: id, comment: newText })
                    })
                        .then(r => r.json())
                        .then(data => {
                            // Восстанавливаем кнопку
                            saveText.classList.remove('hidden');
                            saveLoader.classList.add('hidden');
                            saveBtn.disabled = false;

                            if (data.status === 'success') {
                                // Обновляем текст (используем whitespace-pre-wrap, поэтому вставляем как текст, а не html, чтобы избежать XSS)
                                contentEl.textContent = newText;
                                contentEl.classList.remove('hidden');
                                editUi.classList.add('hidden');
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(() => {
                            saveText.classList.remove('hidden');
                            saveLoader.classList.add('hidden');
                            saveBtn.disabled = false;
                            alert('Произошла ошибка при соединении с сервером.');
                        });
                    return;
                }
            });
        }

        // Закрытие меню при клике вне
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.comment-menu-container')) {
                document.querySelectorAll('.comment-menu').forEach(m => m.classList.add('hidden'));
            }
        });
    });
</script>