<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

// Обновляем все авто с правильной кодировкой
$pdo->exec("SET NAMES utf8");

$updates = [
    ['title' => 'Chery Tiggo 7 Pro Max', 'engine_type' => 'Бензин', 'transmission' => 'Вариатор', 'drive_type' => 'Передний', 'body_type' => 'Кроссовер', 'color' => 'Белый', 'description' => 'Флагманский кроссовер с турбированным двигателем 1.5T'],
    ['title' => 'Haval Jolion', 'engine_type' => 'Бензин', 'transmission' => 'Робот', 'drive_type' => 'Передний', 'body_type' => 'Кроссовер', 'color' => 'Серый', 'description' => 'Компактный городской кроссовер'],
    ['title' => 'Geely Coolray', 'engine_type' => 'Бензин', 'transmission' => 'Робот', 'drive_type' => 'Передний', 'body_type' => 'Кроссовер', 'color' => 'Синий', 'description' => 'Спортивный кроссовер с агрессивным дизайном'],
    ['title' => 'Changan UNI-V', 'engine_type' => 'Бензин', 'transmission' => 'Робот', 'drive_type' => 'Передний', 'body_type' => 'Лифтбек', 'color' => 'Чёрный', 'description' => 'Стильный лифтбек с безрамочными дверями'],
    ['title' => 'BYD Song Plus DM-i', 'engine_type' => 'Гибрид', 'transmission' => 'Вариатор', 'drive_type' => 'Передний', 'body_type' => 'Кроссовер', 'color' => 'Зелёный', 'description' => 'Гибридный кроссовер с запасом хода до 1100 км'],
];

foreach ($updates as $u) {
    $stmt = $pdo->prepare("UPDATE cars SET engine_type=?, transmission=?, drive_type=?, body_type=?, color=?, description=? WHERE title=?");
    $stmt->execute([$u['engine_type'], $u['transmission'], $u['drive_type'], $u['body_type'], $u['color'], $u['description'], $u['title']]);
    echo $u['title'] . ": " . $stmt->rowCount() . " rows updated\n";
}

// Проверяем
$cars = selectAll('cars');
foreach ($cars as $c) {
    echo $c['title'] . ': ' . $c['body_type'] . ' / ' . $c['drive_type'] . ' / ' . $c['color'] . "\n";
}
