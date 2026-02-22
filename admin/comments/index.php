<?php
include "../../path.php";
include "../../app/controllers/commentaries.php";
?>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>Админ — Комментарии | ChinaCars</title>
</head>

<body>

    <?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
    <div class="container">
        <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
        <div class="col-9">
            <h2>Управление комментариями</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Текст</th>
                        <th>Автор</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commentsForAdm as $comment): ?>
                        <tr>
                            <td><?= $comment['id'] ?></td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#commentModal<?= $comment['id'] ?>"
                                    class="text-decoration-none text-light" style="border-bottom: 1px dashed #0f0;">
                                    <?= mb_strlen($comment['comment'], 'UTF-8') > 50 ? mb_substr($comment['comment'], 0, 50, 'UTF-8') . '...' : htmlspecialchars($comment['comment']) ?>
                                </a>
                            </td>
                            <td><?= explode('@', $comment['email'])[0] ?>@</td>
                            <td>
                                <?php if ($comment['status']): ?>
                                    <a href="edit.php?publish=0&pub_id=<?= $comment['id'] ?>"
                                        class="badge bg-success text-decoration-none">Опубликован</a>
                                <?php else: ?>
                                    <a href="edit.php?publish=1&pub_id=<?= $comment['id'] ?>"
                                        class="badge bg-warning text-decoration-none">Скрыт</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?edit_id=<?= $comment['id'] ?>" class="btn btn-sm btn-primary"><i
                                        class="fas fa-edit"></i></a>
                                <a href="edit.php?delete_id=<?= $comment['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Удалить комментарий?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPagesCmd > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($pageCmd > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pageCmd - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesCmd; $i++): ?>
                            <li class="page-item <?= ($i == $pageCmd) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pageCmd < $totalPagesCmd): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pageCmd + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
    </div>

    <!-- Модальные окна для полного текста комментария (вынесены за пределы таблиц и контейнеров для избежания конфликтов z-index) -->
    <?php foreach ($commentsForAdm as $comment): ?>
        <div class="modal fade" id="commentModal<?= $comment['id'] ?>" tabindex="-1"
            aria-labelledby="commentModalLabel<?= $comment['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: rgba(0, 20, 0, 0.9); border: 1px solid #0f0;">
                    <div class="modal-header" style="border-bottom: 1px solid #0f0;">
                        <h5 class="modal-title" id="commentModalLabel<?= $comment['id'] ?>" style="color:#0f0;">Комментарий
                            #<?= $comment['id'] ?> (от
                            <?= explode('@', $comment['email'])[0] ?>@)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body"
                        style="color:#0f0; white-space: pre-wrap; font-family: 'Courier New', Courier, monospace;">
                        <?= htmlspecialchars($comment['comment']) ?>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #0f0;">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal"
                            style="color:#0f0; border-color:#0f0;">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>