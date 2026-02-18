<?php
include "../../path.php";
include SITE_ROOT . "/app/controllers/cars.php";
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
    <title>Админ — Автомобили | ChinaCars</title>
</head>

<body>

    <?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
    <div class="container">
        <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
        <div class="col-9">
            <h2>Управление автомобилями</h2>
            <a href="<?php echo BASE_URL; ?>admin/cars/create.php" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Добавить авто
            </a>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Бренд</th>
                        <th>Цена</th>
                        <th>Добавил</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carsAdm as $car): ?>
                        <tr>
                            <td>
                                <?= $car['id'] ?>
                            </td>
                            <td>
                                <?php if ($car['img']): ?>
                                    <img src="<?= BASE_URL ?>assets/images/cars/<?= $car['img'] ?>" width="60" alt="">
                                <?php else: ?>
                                    <i class="fas fa-car"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $car['title'] ?>
                            </td>
                            <td>
                                <?= $car['brand_name'] ?>
                            </td>
                            <td>
                                <?= number_format($car['price'], 0, '', ' ') ?> &#8381;
                            </td>
                            <td>
                                <?= $car['username'] ?>
                            </td>
                            <td>
                                <?php if ($car['status'] == 1): ?>
                                    <a href="<?= BASE_URL ?>admin/cars/index.php?pub_id=<?= $car['id'] ?>&publish=0"
                                        class="badge bg-success text-decoration-none">Опубликован</a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>admin/cars/index.php?pub_id=<?= $car['id'] ?>&publish=1"
                                        class="badge bg-warning text-decoration-none">Черновик</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/cars/edit.php?id=<?= $car['id'] ?>"
                                    class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>admin/cars/index.php?delete_id=<?= $car['id'] ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('Удалить авто?')"><i
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