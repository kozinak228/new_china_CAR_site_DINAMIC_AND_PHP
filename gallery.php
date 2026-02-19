<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";

$allCars = selectAll('cars', ['status' => 1]);
$brands = selectAll('brands');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>Галерея — ChinaCars</title>
</head>
<body>

    <?php include("app/include/header.php"); ?>

    <div class="container">
        <div class="content row">
            <div class="main-content col-12">
                <h2>Галерея автомобилей</h2>
                <div class="row gallery-grid">
                    <?php foreach ($allCars as $car): ?>
                        <?php
                        $images = selectCarImages($car['id']);
                        // Показываем главное фото
                        if ($car['img']):
                            ?>
                            <div class="col-md-4 col-6 mb-3">
                                <a href="<?= BASE_URL . 'single.php?id=' . $car['id'] ?>">
                                    <div class="gallery-item">
                                        <img src="<?= BASE_URL . 'assets/images/cars/' . $car['img'] ?>" alt="<?= $car['title'] ?>"
                                            class="img-fluid">
                                        <div class="gallery-overlay">
                                            <span>
                                                <?= $car['title'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        endif;
                        // Показываем доп. фото из галереи
                        foreach ($images as $image):
                            ?>
                            <div class="col-md-4 col-6 mb-3">
                                <a href="<?= BASE_URL . 'single.php?id=' . $car['id'] ?>">
                                    <div class="gallery-item">
                                        <img src="<?= BASE_URL . 'assets/images/cars/' . $image['img'] ?>"
                                            alt="<?= $car['title'] ?>" class="img-fluid">
                                        <div class="gallery-overlay">
                                            <span>
                                                <?= $car['title'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                    <?php if (count($allCars) == 0): ?>
                        <div class="col-12">
                            <p>В галерее пока нет фотографий.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("app/include/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0"
        crossorigin="anonymous"></script>
</body>
</html>