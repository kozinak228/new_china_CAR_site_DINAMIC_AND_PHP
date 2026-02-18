<?php
include "../../path.php";
include SITE_ROOT . "/app/controllers/brands.php";
include SITE_ROOT . "/app/controllers/users.php";
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
    <title>Админ — Бренды | ChinaCars</title>
</head>

<body>

    <?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
    <div class="container">
        <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
        <div class="col-9">
            <h2>Управление брендами</h2>
            <a href="<?php echo BASE_URL; ?>admin/brands/create.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Добавить бренд
            </a>

            <?php if ($errMsg): ?>
                <div class="alert alert-danger">
                    <?= $errMsg ?>
                </div>
            <?php endif; ?>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Логотип</th>
                        <th>Название</th>
                        <th>Страна</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brands as $b): ?>
                        <tr>
                            <td>
                                <?= $b['id'] ?>
                            </td>
                            <td>
                                <?php if ($b['logo']): ?>
                                    <img src="<?= BASE_URL ?>assets/images/brands/<?= $b['logo'] ?>" width="40" alt="">
                                <?php else: ?>
                                    <i class="fas fa-car"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $b['name'] ?>
                            </td>
                            <td>
                                <?= $b['country'] ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/brands/edit.php?id=<?= $b['id'] ?>"
                                    class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>admin/brands/index.php?del_id=<?= $b['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Удалить бренд и все его авто?')"><i
                                        class="fas fa-trash"></i></a>
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