<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

header('Content-Type: application/json');

$query = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($query) || mb_strlen($query, 'UTF-8') < 2) {
    echo json_encode([]);
    exit;
}

$cars = searchCars($query);
$total = count($cars);
$results = array_slice($cars, 0, 7);

$items = [];
foreach ($results as $car) {
    $items[] = [
        'id' => $car['id'],
        'title' => $car['title'],
        'brand' => $car['brand_name'],
        'price' => number_format($car['price'], 0, '', ' ') . ' ₽',
        'url' => BASE_URL . "single.php?id=" . $car['id'],
        'img' => $car['img'] ? BASE_URL . "assets/images/cars/" . $car['img'] : null
    ];
}

echo json_encode([
    'items' => $items,
    'total' => $total,
    'search_url' => BASE_URL . "search.php?term=" . urlencode($query)
]);
