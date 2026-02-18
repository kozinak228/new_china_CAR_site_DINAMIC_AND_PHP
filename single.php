<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$car = selectCarById($id);
if (!$car) {
    header('location: ' . BASE_URL);
    exit;
}
$carImages = selectCarImages($id);
$brands = selectAll('brands');
?>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title><?= $car['title'] ?> &mdash; ChinaCars</title>
</head>

<body>

    <?php include("app/include/header.php"); ?>

    <div class="container">
        <div class="content row">
            <div class="main-content col-md-9 col-12">
                <h2><?= $car['title'] ?></h2>

                <div class="single_post row">
                    <div class="img col-12 mb-3">
                        <?php if ($car['img']): ?>
                            <img src="<?= BASE_URL . 'assets/images/cars/' . $car['img'] ?>" alt="<?= $car['title'] ?>"
                                class="img-thumbnail w-100">
                        <?php else: ?>
                            <div class="car-no-img-big"><i class="fas fa-car fa-5x"></i></div>
                        <?php endif; ?>
                    </div>

                    <?php if (count($carImages) > 0): ?>
                        <div class="col-12 mb-3">
                            <h4>Фотогалерея</h4>
                            <div class="row gallery-row">
                                <?php foreach ($carImages as $image): ?>
                                    <div class="col-3 mb-2">
                                        <a href="<?= BASE_URL . 'assets/images/cars/' . $image['img'] ?>" target="_blank">
                                            <img src="<?= BASE_URL . 'assets/images/cars/' . $image['img'] ?>" alt="Фото"
                                                class="img-thumbnail gallery-thumb">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12 mb-3">
                        <div class="car-price-big">
                            <?= number_format($car['price'], 0, '', ' ') ?> &#8381;
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <h4>Технические характеристики</h4>
                        <table class="table table-striped specs-table">
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-trademark"></i> Бренд</td>
                                    <td><strong><?= $car['brand_name'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar"></i> Год выпуска</td>
                                    <td><?= $car['year'] ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-road"></i> Пробег</td>
                                    <td><?= number_format($car['mileage'], 0, '', ' ') ?> км</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-gas-pump"></i> Тип двигателя</td>
                                    <td><?= $car['engine_type'] ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-cog"></i> Объём двигателя</td>
                                    <td><?= $car['engine_volume'] ?> л</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tachometer-alt"></i> Мощность</td>
                                    <td><?= $car['horsepower'] ?> л.с.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-exchange-alt"></i> КПП</td>
                                    <td><?= $car['transmission'] ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-cogs"></i> Привод</td>
                                    <td><?= $car['drive_type'] ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-car"></i> Тип кузова</td>
                                    <td><?= $car['body_type'] ?></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-palette"></i> Цвет</td>
                                    <td><?= $car['color'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($car['description'])): ?>
                        <div class="col-12 mb-3">
                            <h4>Описание</h4>
                            <div class="single_post_text">
                                <?= $car['description'] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info">
                        <i class="far fa-user"> <?= $car['username']; ?></i>
                        <i class="far fa-calendar"> <?= $car['created_date']; ?></i>
                    </div>

                    <?php
                    $page = $car['id'];
                    $_GET['post'] = $car['id'];
                    include("app/include/comments.php");
                    ?>
                </div>
            </div>

            <div class="sidebar col-md-3 col-12">
                <div class="section search">
                    <h3>Поиск</h3>
                    <form action="search.php" method="post">
                        <input type="text" name="search-term" class="text-input" placeholder="Марка, модель...">
                    </form>
                </div>

                <div class="section topics">
                    <h3>Бренды</h3>
                    <ul>
                        <?php foreach ($brands as $b): ?>
                            <li>
                                <a href="<?= BASE_URL . 'category.php?id=' . $b['id']; ?>"><?= $b['name']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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