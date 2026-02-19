<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search-term'])) {
    $cars = searchCars($_POST['search-term']);
    $searchTerm = htmlspecialchars($_POST['search-term']);
} else {
    $cars = [];
    $searchTerm = '';
}
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
    <title>Поиск — ChinaCars</title>
</head>

<body>

    <?php include("app/include/header.php"); ?>

    <div class="container">
        <div class="content row">
            <div class="main-content col-12">
                <h2>Результаты поиска <?= $searchTerm ? 'по запросу: «' . $searchTerm . '»' : '' ?></h2>
                <div class="row">
                    <?php if (count($cars) > 0): ?>
                        <?php foreach ($cars as $car): ?>
                            <div class="col-md-4 col-12 mb-4">
                                <div class="car-card">
                                    <div class="car-card-img">
                                        <?php if ($car['img']): ?>
                                            <img src="<?= BASE_URL . 'assets/images/cars/' . $car['img'] ?>" alt="<?= $car['title'] ?>"
                                                class="img-fluid">
                                        <?php else: ?>
                                            <div class="car-no-img"><i class="fas fa-car fa-3x"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="car-card-body">
                                        <span class="car-brand-badge"><?= $car['brand_name'] ?></span>
                                        <h5><a href="<?= BASE_URL . 'single.php?id=' . $car['id']; ?>"><?= $car['title'] ?></a></h5>
                                        <div class="car-specs">
                                            <span><i class="fas fa-calendar"></i> <?= $car['year'] ?></span>
                                            <span><i class="fas fa-cog"></i> <?= $car['engine_volume'] ?>л /
                                                <?= $car['horsepower'] ?> л.с.</span>
                                        </div>
                                        <div class="car-price">
                                            <?= number_format($car['price'], 0, '', ' ') ?> &#8381;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p>Ничего не найдено. Попробуйте другой запрос.</p>
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