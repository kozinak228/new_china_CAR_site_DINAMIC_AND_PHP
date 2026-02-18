<?php
include "../../path.php";
include SITE_ROOT . "/app/controllers/cars.php";
include SITE_ROOT . "/app/controllers/users.php";

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header('location: ' . BASE_URL . 'admin/cars/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $car = selectOne('cars', ['id' => $_GET['id']]);
    $carImages = selectCarImages($_GET['id']);
} elseif (isset($_POST['id'])) {
    $car = selectOne('cars', ['id' => $_POST['id']]);
    $carImages = selectCarImages($_POST['id']);
}
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
    <title>Редактировать авто | ChinaCars</title>
</head>

<body>

    <?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
    <div class="container">
        <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
        <div class="col-9">
            <h2>Редактировать:
                <?= $car['title'] ?>
            </h2>

            <?php if (!empty($errMsg)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errMsg as $err): ?>
                        <p>
                            <?= $err ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="edit.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $car['id'] ?>">
                <input type="hidden" name="current_img" value="<?= $car['img'] ?>">

                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label">Название авто *</label>
                        <input type="text" name="title" class="form-control" value="<?= $car['title'] ?>" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Бренд *</label>
                        <select name="brand" class="form-select" required>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= ($car['id_brand'] == $b['id']) ? 'selected' : '' ?>>
                                    <?= $b['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Цена (&#8381;)</label>
                        <input type="number" name="price" class="form-control" step="0.01" value="<?= $car['price'] ?>">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Год выпуска</label>
                        <input type="number" name="year" class="form-control" value="<?= $car['year'] ?>" min="2000"
                            max="2030">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Пробег (км)</label>
                        <input type="number" name="mileage" class="form-control" value="<?= $car['mileage'] ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Тип двигателя</label>
                        <select name="engine_type" class="form-select">
                            <?php foreach (['Бензин', 'Дизель', 'Электро', 'Гибрид'] as $et): ?>
                                <option value="<?= $et ?>" <?= ($car['engine_type'] == $et) ? 'selected' : '' ?>>
                                    <?= $et ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Объём (л)</label>
                        <input type="number" name="engine_volume" class="form-control" step="0.1"
                            value="<?= $car['engine_volume'] ?>">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Мощность (л.с.)</label>
                        <input type="number" name="horsepower" class="form-control" value="<?= $car['horsepower'] ?>">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">КПП</label>
                        <select name="transmission" class="form-select">
                            <?php foreach (['АКПП', 'МКПП', 'Робот', 'Вариатор'] as $tr): ?>
                                <option value="<?= $tr ?>" <?= ($car['transmission'] == $tr) ? 'selected' : '' ?>>
                                    <?= $tr ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Привод</label>
                        <select name="drive_type" class="form-select">
                            <?php foreach (['Передний', 'Задний', 'Полный'] as $dt): ?>
                                <option value="<?= $dt ?>" <?= ($car['drive_type'] == $dt) ? 'selected' : '' ?>>
                                    <?= $dt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Тип кузова</label>
                        <select name="body_type" class="form-select">
                            <?php foreach (['Кроссовер', 'Седан', 'Хэтчбек', 'Лифтбек', 'Универсал', 'Внедорожник', 'Минивэн'] as $bt): ?>
                                <option value="<?= $bt ?>" <?= ($car['body_type'] == $bt) ? 'selected' : '' ?>>
                                    <?= $bt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Цвет</label>
                        <input type="text" name="color" class="form-control" value="<?= $car['color'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="5"><?= $car['description'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Текущее фото</label>
                    <div>
                        <?php if ($car['img']): ?>
                            <img src="<?= BASE_URL ?>assets/images/cars/<?= $car['img'] ?>" width="200"
                                class="img-thumbnail" alt="">
                        <?php else: ?>
                            <p>Нет фото</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Заменить главное фото</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                </div>

                <!-- Текущая галерея -->
                <?php if (!empty($carImages)): ?>
                    <div class="mb-3">
                        <label class="form-label">Фотогалерея</label>
                        <div class="row">
                            <?php foreach ($carImages as $image): ?>
                                <div class="col-3 mb-2 text-center">
                                    <img src="<?= BASE_URL ?>assets/images/cars/<?= $image['img'] ?>" class="img-thumbnail"
                                        width="100" alt="">
                                    <br>
                                    <a href="<?= BASE_URL ?>admin/cars/index.php?del_img_id=<?= $image['id'] ?>&car_id=<?= $car['id'] ?>"
                                        class="btn btn-sm btn-danger mt-1" onclick="return confirm('Удалить фото?')"><i
                                            class="fas fa-trash"></i></a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Добавить фото в галерею</label>
                    <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="publish" id="publish" <?= ($car['status'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="publish">Опубликовать</label>
                </div>

                <button type="submit" name="edit_car" class="btn btn-primary">
                    <i class="fas fa-save"></i> Сохранить
                </button>
                <a href="<?= BASE_URL ?>admin/cars/index.php" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
    </div>

</body>

</html>