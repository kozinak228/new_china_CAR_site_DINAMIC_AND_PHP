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
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Админ — Комментарии | ChinaCars</title>
</head>
<body>

<?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
<div class="container">
    <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
    <div class="col-9">
        <h2>Управление комментариями</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?=$_SESSION['error']?></div>
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
                    <td><?= mb_substr($comment['comment'], 0, 50, 'UTF-8') . '...' ?></td>
                    <td><?= explode('@', $comment['email'])[0] ?>@</td>
                    <td>
                        <?php if ($comment['status']): ?>
                            <a href="edit.php?publish=0&pub_id=<?=$comment['id']?>" class="badge bg-success text-decoration-none">Опубликован</a>
                        <?php else: ?>
                            <a href="edit.php?publish=1&pub_id=<?=$comment['id']?>" class="badge bg-warning text-decoration-none">Скрыт</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?=$comment['id']?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="edit.php?delete_id=<?=$comment['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить комментарий?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

</body>
</html>