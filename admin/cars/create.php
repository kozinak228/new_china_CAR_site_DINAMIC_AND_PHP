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
    <title>Добавить авто | ChinaCars</title>
</head>

<body>

    <?php include(SITE_ROOT . "/app/include/header-admin.php"); ?>
    <div class="container">
        <?php include(SITE_ROOT . "/app/include/sidebar-admin.php"); ?>
        <div class="col-9">
            <h2>Добавить автомобиль</h2>

            <?php if (!empty($errMsg)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errMsg as $err): ?>
                        <p>
                            <?= $err ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="create.php" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label">Название авто *</label>
                        <input type="text" name="title" class="form-control" value="<?= $title ?>" required
                            placeholder="Например: Chery Tiggo 7 Pro Max">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Бренд *</label>
                        <select name="brand" class="form-select" required>
                            <option value="">Выберите бренд</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= ($brand == $b['id']) ? 'selected' : '' ?>>
                                    <?= $b['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Цена (&#8381;)</label>
                        <input type="number" name="price" class="form-control" step="0.01" placeholder="2890000">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Год выпуска</label>
                        <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" min="2000"
                            max="2030">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Пробег (км)</label>
                        <input type="number" name="mileage" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Тип двигателя</label>
                        <select name="engine_type" class="form-select">
                            <option value="Бензин">Бензин</option>
                            <option value="Дизель">Дизель</option>
                            <option value="Электро">Электро</option>
                            <option value="Гибрид">Гибрид</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Объём (л)</label>
                        <input type="number" name="engine_volume" class="form-control" step="0.1" min="0" max="9.9"
                            placeholder="1.5">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">Мощность (л.с.)</label>
                        <input type="number" name="horsepower" class="form-control" min="0" placeholder="147">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label class="form-label">КПП</label>
                        <select name="transmission" class="form-select">
                            <option value="АКПП">АКПП</option>
                            <option value="МКПП">МКПП</option>
                            <option value="Робот">Робот</option>
                            <option value="Вариатор">Вариатор</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Привод</label>
                        <select name="drive_type" class="form-select">
                            <option value="Передний">Передний</option>
                            <option value="Задний">Задний</option>
                            <option value="Полный">Полный</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Тип кузова</label>
                        <select name="body_type" class="form-select">
                            <option value="Кроссовер">Кроссовер</option>
                            <option value="Седан">Седан</option>
                            <option value="Хэтчбек">Хэтчбек</option>
                            <option value="Лифтбек">Лифтбек</option>
                            <option value="Универсал">Универсал</option>
                            <option value="Внедорожник">Внедорожник</option>
                            <option value="Минивэн">Минивэн</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Цвет</label>
                        <input type="text" name="color" class="form-control" placeholder="Белый">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="5"
                        placeholder="Подробное описание автомобиля..."><?= $description ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Главное фото</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label">Доп. фото (галерея, можно выбрать несколько)</label>
                    <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="publish" id="publish">
                    <label class="form-check-label" for="publish">Опубликовать сразу</label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="featured" id="featured">
                    <label class="form-check-label" for="featured">Показывать в карусели (лучшие предложения)</label>
                </div>

                <button type="submit" name="add_car" class="btn btn-success">
                    <i class="fas fa-plus"></i> Добавить авто
                </button>
                <a href="<?= BASE_URL ?>admin/cars/index.php" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
    </div>

</body>

</html>