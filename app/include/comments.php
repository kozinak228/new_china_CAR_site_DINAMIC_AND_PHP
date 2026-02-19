<div class="cpl-md-12 col-12 comments" id="comments">
    <h3>Оставить комментарий</h3>

    <?php if (!empty($errMsg)): ?>
        <div class="alert alert-danger mb-3">
            <?php foreach ($errMsg as $e): ?>
                <p class="mb-0"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL . "single.php?id=$page" ?>" method="post">
        <input type="hidden" name="page" value="<?= (int) $page ?>">

        <?php if (isset($_SESSION['id'])): ?>
            <div class="mb-3">
                <label class="form-label">Вы вошли как: <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></label>
                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['login']) ?>">
            </div>
        <?php else: ?>
            <div class="mb-3">
                <label for="commentEmail" class="form-label">Email адрес</label>
                <input name="email" type="email" class="form-control" id="commentEmail" placeholder="name@example.com"
                    required>
            </div>
        <?php endif; ?>

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
    </form>

    <?php
    // Загружаем комментарии для этой машины
    $comments = selectAll('comments', ['page' => $page, 'status' => 1]);
    ?>

    <?php if (!empty($comments)): ?>
        <div class="row all-comments mt-4">
            <h3 class="col-12">Отзывы об автомобиле</h3>
            <?php foreach ($comments as $c): ?>
                <div class="one-comment col-12">
                    <span><i class="far fa-user"> <?= htmlspecialchars($c['email']) ?></i></span>
                    <span><i class="far fa-calendar-check"> <?= $c['created_date'] ?></i></span>
                    <div class="col-12 text">
                        <?= nl2br(htmlspecialchars($c['comment'])) ?>
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
</script>