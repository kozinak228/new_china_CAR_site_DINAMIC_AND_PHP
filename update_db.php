<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

try {
    global $pdo;

    // Аватарки и биография
    $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;");
    echo "Колонка avatar добавлена в таблицу users.\n";

    $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL;");
    echo "Колонка bio добавлена в таблицу users.\n";

    // Сортировка для галереи
    $pdo->exec("ALTER TABLE car_images ADD COLUMN sort_order INT DEFAULT 0;");
    echo "Колонка sort_order добавлена в таблицу car_images.\n";

    echo "Успех!";
} catch (PDOException $e) {
    echo "Ошибка (возможно колонки уже существуют): " . $e->getMessage();
}
