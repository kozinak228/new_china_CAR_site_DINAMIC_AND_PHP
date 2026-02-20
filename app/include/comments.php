<div class="cpl-md-12 col-12 comments" id="comments">
    <h3>Оставить комментарий</h3>

    <?php if (!empty($errMsg)): ?>
        <div class="alert alert-danger mb-3">
            <?php foreach ($errMsg as $e): ?>
                <p class="mb-0"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="ajaxErrorBlock" class="alert alert-danger mb-3" style="display: none;"></div>

    <form id="ajaxCommentForm" action="<?= BASE_URL . "single.php?id=$page" ?>" method="post">
        <input type="hidden" name="page" id="commentPageId" value="<?= (int) $page ?>">

        <?php if (isset($_SESSION['id'])): ?>
            <div class="mb-3">
                <label class="form-label">Вы вошли как: <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></label>
                <input type="hidden" name="email" id="commentEmail"
                    value="<?= htmlspecialchars($_SESSION['email'] ?? $_SESSION['login']) ?>">
            </div>

            <div class="mb-1">
                <label for="commentText" class="form-label">Напишите ваш комментарий</label>
                <textarea name="comment" class="form-control comment-textarea" id="commentText" rows="4"
                    placeholder="Минимум 10 символов..." required></textarea>
            </div>
            <div class="comment-counter mb-3">
                <span id="charCount">0</span> / мин. 10 символов
            </div>

            <div class="col-12">
                <button type="submit" name="goComment" class="btn btn-primary">Отправить</button>
            </div>
        <?php else: ?>
            <div class="mb-1">
                <label for="commentText" class="form-label">Напишите ваш комментарий</label>
                <textarea name="comment" class="form-control comment-textarea" id="commentText" rows="4"
                    placeholder="Минимум 10 символов..."></textarea>
            </div>
            <div class="comment-counter mb-3">
                <span id="charCount">0</span> / мин. 10 символов
            </div>

            <div class="col-12">
                <button type="button" class="btn btn-secondary" style="cursor: not-allowed; opacity: 0.65;"
                    onclick="document.getElementById('authMessage').style.display='block';">Отправить</button>
            </div>
            <div id="authMessage" class="text-danger mt-2" style="display: none; font-weight: 500;">
                Сначала <a href="<?= BASE_URL ?>log.php" class="text-decoration-underline text-danger">авторизуйтесь</a> или
                <a href="<?= BASE_URL ?>reg.php" class="text-decoration-underline text-danger">зарегистрируйтесь</a>, чтобы
                оставить комментарий.
            </div>
        <?php endif; ?>
    </form>

    <?php
    // Загружаем комментарии для этой машины с аватарками пользователей
    global $pdo;
    $sql = "SELECT c.*, u.avatar 
            FROM comments c 
            LEFT JOIN users u ON c.email = u.email OR c.email = u.username 
            WHERE c.page = ? AND c.status = 1 
            ORDER BY c.id DESC";
    $query = $pdo->prepare($sql);
    $query->execute([$page]);
    $comments = $query->fetchAll();
    ?>

    <?php if (!empty($comments)): ?>
        <div class="row all-comments mt-4">
            <h3 class="col-12">Отзывы об автомобиле</h3>
            <?php foreach ($comments as $c): ?>
                <div class="one-comment col-12 d-flex align-items-start mb-3"
                    style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                    <div class="me-3">
                        <?php if (!empty($c['avatar'])): ?>
                            <img src="<?= BASE_URL ?>assets/images/avatars/<?= htmlspecialchars($c['avatar']) ?>" width="50"
                                height="50" class="rounded-circle shadow-sm" style="object-fit:cover;" alt="Аватар">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-3x text-secondary"></i>
                        <?php endif; ?>
                    </div>
                    <div class="w-100">
                        <div class="mb-1 text-muted small">
                            <span class="fw-bold" style="color: var(--primary-color);"><i class="far fa-user"></i>
                                <?= htmlspecialchars($c['email']) ?></span>
                            <span class="ms-3"><i class="far fa-calendar-check"></i> <?= $c['created_date'] ?></span>
                        </div>
                        <div class="text mt-2">
                            <?= nl2br(htmlspecialchars($c['comment'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Счётчик символов для комментария
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');
    if (commentText) {
        commentText.addEventListener('input', function () {
            const len = this.value.length;
            charCount.textContent = len;
            charCount.style.color = len >= 10 ? '#28a745' : '#e94560';
        });
    }

    // AJAX отправка комментария (только для авторизованных)
    <?php if (isset($_SESSION['id'])): ?>
        const commentForm = document.getElementById('ajaxCommentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const errorBlock = document.getElementById('ajaxErrorBlock');
                errorBlock.style.display = 'none';
                errorBlock.innerHTML = '';

                const pageId = document.getElementById('commentPageId').value;
                const emailInput = document.getElementById('commentEmail') ? document.getElementById('commentEmail').value : document.querySelector('input[name="email"]').value;
                const commentVal = document.getElementById('commentText').value;

                fetch('<?= BASE_URL ?>ajax_comments.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        page: pageId,
                        email: emailInput,
                        comment: commentVal
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Успешно добавлено
                            document.getElementById('commentText').value = '';
                            charCount.textContent = '0';
                            charCount.style.color = '#e94560';

                            // Добавляем новый коммент в список
                            let commentsContainer = document.querySelector('.all-comments');
                            if (!commentsContainer) {
                                // Если это первый комментарий на странице
                                commentsContainer = document.createElement('div');
                                commentsContainer.className = 'row all-comments mt-4';
                                commentsContainer.innerHTML = '<h3 class="col-12">Отзывы об автомобиле</h3>';
                                document.getElementById('comments').appendChild(commentsContainer);
                            }

                            // Вставляем сразу после заголовка (наверх списка)
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const newCommentNode = tempDiv.firstChild;

                            const header = commentsContainer.querySelector('h3');
                            if (header && header.nextSibling) {
                                commentsContainer.insertBefore(newCommentNode, header.nextSibling);
                            } else {
                                commentsContainer.appendChild(newCommentNode);
                            }

                            // Плавный скролл к новому комментарию
                            newCommentNode.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        } else {
                            // Вывод ошибки
                            errorBlock.innerHTML = '<p class="mb-0">' + data.message + '</p>';
                            errorBlock.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorBlock.innerHTML = '<p class="mb-0">Произошла неизвестная ошибка сети.</p>';
                        errorBlock.style.display = 'block';
                    });
            });
        }
    <?php endif; ?>
</script>