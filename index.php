<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

$brand_id = isset($_GET['brand']) ? intval($_GET['brand']) : null;
$body_type = isset($_GET['body_type']) ? trim($_GET['body_type']) : null;
$price_min = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? floatval($_GET['price_min']) : null;
$price_max = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? floatval($_GET['price_max']) : null;

$filters = [];
if ($brand_id)
    $filters['brand'] = $brand_id;
if ($body_type)
    $filters['body_type'] = $body_type;
if ($price_min)
    $filters['price_min'] = $price_min;
if ($price_max)
    $filters['price_max'] = $price_max;

$cars = selectCarsForCatalog($perPage, $offset, $filters);
$totalCars = countCars($filters);
$totalPages = ceil($totalCars / $perPage);
$featured = selectFeaturedCars(5);
$brands = selectAll('brands');
$bodyTypes = getBodyTypes();
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
    <title>ChinaCars &mdash; Каталог автомобилей из Китая</title>
</head>

<body class="<?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'dark-theme' : '' ?>">

    <?php include("app/include/header.php"); ?>

    <div class="container">
        <?php if (count($featured) > 0 && $page == 1 && empty($filters)): ?>
            <h2 class="slider-title">Лучшие предложения</h2>
            <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($featured as $key => $f): ?>
                        <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                            <?php if ($f['img']): ?>
                                <img src="<?= BASE_URL ?>assets/images/cars/<?= $f['img'] ?>" class="d-block w-100"
                                    alt="<?= $f['title'] ?>">
                            <?php else: ?>
                                <div class="car-no-img-big"><i class="fas fa-car fa-5x"></i></div>
                            <?php endif; ?>
                            <div class="carousel-caption carousel-caption-hack">
                                <h5><a href="<?= BASE_URL ?>single.php?id=<?= $f['id'] ?>"><?= $f['title'] ?></a></h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="main-content col-md-9 col-12">
                <h2>Каталог автомобилей</h2>
                <div class="row">
                    <?php if (count($cars) > 0): ?>
                        <?php foreach ($cars as $car): ?>
                            <div class="col-md-6 col-12 mb-4">
                                <div class="car-card">
                                    <div class="car-card-img">
                                        <?php if ($car['img']): ?>
                                            <a href="<?= BASE_URL ?>single.php?id=<?= $car['id'] ?>">
                                                <img src="<?= BASE_URL ?>assets/images/cars/<?= $car['img'] ?>"
                                                    alt="<?= $car['title'] ?>">
                                            </a>
                                        <?php else: ?>
                                            <div class="car-no-img"><i class="fas fa-car fa-3x"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="car-card-body">
                                        <span class="car-brand-badge"><?= $car['brand_name'] ?></span>
                                        <h5><a href="<?= BASE_URL ?>single.php?id=<?= $car['id'] ?>"><?= $car['title'] ?></a>
                                        </h5>
                                        <div class="car-specs">
                                            <span><i class="fas fa-calendar"></i> <?= $car['year'] ?></span>
                                            <span><i class="fas fa-cog"></i> <?= $car['engine_volume'] ?>&#1083; /
                                                <?= $car['horsepower'] ?> &#1083;.&#1089;.</span>
                                            <span><i class="fas fa-road"></i> <?= $car['body_type'] ?></span>
                                        </div>
                                        <div class="car-price">
                                            <?= number_format($car['price'], 0, '', ' ') ?> &#8381;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Автомобили не найдены</p>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php
                                $params = ['page' => $i];
                                if ($brand_id)
                                    $params['brand'] = $brand_id;
                                if ($body_type)
                                    $params['body_type'] = $body_type;
                                if ($price_min)
                                    $params['price_min'] = $price_min;
                                if ($price_max)
                                    $params['price_max'] = $price_max;
                                ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query($params) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <div class="sidebar col-md-3 col-12">
                <div class="section search">
                    <h3>Поиск</h3>
                    <form action="search.php" method="post">
                        <input type="text" name="search-term" class="text-input" placeholder="Марка, модель...">
                    </form>
                </div>

                <div class="section topics brands-section">
                    <h3 class="brands-toggle" onclick="toggleBrands()">Бренды <span id="brands-arrow">▼</span></h3>
                    <input type="text" id="brand-search" class="brand-search-input" placeholder="Поиск бренда..."
                        oninput="filterBrands()">
                    <ul id="brands-list">
                        <li><a href="<?= BASE_URL ?>">Все бренды</a></li>
                        <?php foreach ($brands as $i => $b): ?>
                            <li class="brand-item <?= $i >= 5 ? 'brand-hidden' : '' ?>"
                                data-name="<?= mb_strtolower($b['name']) ?>">
                                <a href="<?= BASE_URL ?>category.php?id=<?= $b['id'] ?>"><?= $b['name'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($brands) > 5): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2" id="brands-show-all"
                            onclick="showAllBrands()">Показать все (<?= count($brands) ?>)</button>
                    <?php endif; ?>
                </div>

                <div class="section">
                    <h3>Фильтры</h3>
                    <form action="index.php" method="get">
                        <div class="mb-2">
                            <label class="form-label">Тип кузова</label>
                            <select name="body_type" class="form-select form-select-sm">
                                <option value="">Все</option>
                                <?php foreach ($bodyTypes as $bt): ?>
                                    <option value="<?= $bt ?>" <?= ($body_type == $bt) ? 'selected' : '' ?>><?= $bt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Цена от</label>
                            <input type="number" name="price_min" class="form-control form-control-sm"
                                value="<?= $price_min ?>" placeholder="от" min="0">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Цена до</label>
                            <input type="number" name="price_max" class="form-control form-control-sm"
                                value="<?= $price_max ?>" placeholder="до" min="0">
                        </div>
                        <button type="submit" class="btn btn-sm btn-secondary w-100 mt-2">Применить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("app/include/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0"
        crossorigin="anonymous"></script>
    <script>
        let brandsExpanded = false;
        let brandsCollapsed = false;

        function toggleBrands() {
            const list = document.getElementById('brands-list');
            const arrow = document.getElementById('brands-arrow');
            const search = document.getElementById('brand-search');
            const showAllBtn = document.getElementById('brands-show-all');
            brandsCollapsed = !brandsCollapsed;
            if (brandsCollapsed) {
                list.style.display = 'none';
                search.style.display = 'none';
                if (showAllBtn) showAllBtn.style.display = 'none';
                arrow.textContent = '▶';
            } else {
                list.style.display = '';
                search.style.display = '';
                if (showAllBtn) showAllBtn.style.display = '';
                arrow.textContent = '▼';
            }
        }

        function showAllBrands() {
            const items = document.querySelectorAll('.brand-item');
            const btn = document.getElementById('brands-show-all');
            brandsExpanded = !brandsExpanded;
            items.forEach(item => {
                if (brandsExpanded) {
                    item.classList.remove('brand-hidden');
                } else {
                    // Скрыть обратно элементы с индексом >= 5
                    const allItems = Array.from(document.querySelectorAll('.brand-item'));
                    const idx = allItems.indexOf(item);
                    if (idx >= 5) item.classList.add('brand-hidden');
                }
            });
            btn.textContent = brandsExpanded ? 'Свернуть' : 'Показать все (' + document.querySelectorAll('.brand-item').length + ')';
        }

        function filterBrands() {
            const query = document.getElementById('brand-search').value.toLowerCase();
            const items = document.querySelectorAll('.brand-item');
            const btn = document.getElementById('brands-show-all');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                if (query === '') {
                    // Вернуть начальное состояние
                    if (!brandsExpanded) {
                        const allItems = Array.from(document.querySelectorAll('.brand-item'));
                        const idx = allItems.indexOf(item);
                        item.style.display = idx >= 5 ? 'none' : '';
                    } else {
                        item.style.display = '';
                    }
                } else {
                    item.style.display = name.includes(query) ? '' : 'none';
                }
            });
            if (btn) btn.style.display = query ? 'none' : '';
        }
    </script>
</body>

</html>