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

            <form action="index.php" method="post" id="bulkForm">
                <div class="d-flex mb-3 align-items-center">
                    <select name="bulk_action" class="form-select w-auto me-2" required>
                        <option value="">Выберите действие...</option>
                        <option value="publish">Опубликовать выбранные</option>
                        <option value="draft">Убрать в черновик</option>
                        <option value="delete">Удалить выбранные</option>
                    </select>
                    <button type="submit" name="apply_bulk" class="btn btn-warning btn-sm"
                        onclick="return confirm('Вы уверены, что хотите применить действие к выбранным записям?')">Применить</button>
                </div>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
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
                                <td><input type="checkbox" name="selected_ids[]" value="<?= $car['id'] ?>"
                                        class="rowCheckbox"></td>
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
            </form>

            <?php if ($totalPagesAdm > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPagesAdm; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPagesAdm): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>

    <!-- Скрипт для выделения всех чекбоксов -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.rowCheckbox');

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                });
            }
        });
    </script>

</body>

</html>