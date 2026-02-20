<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

try {
    // 1. Добавление индексов (может выдать ошибку, если уже есть, поэтому через TRY CATCH по каждому)
    $queries_indexes = [
        "ALTER TABLE cars ADD INDEX idx_status (status)",
        "ALTER TABLE cars ADD INDEX idx_id_brand (id_brand)",
        "ALTER TABLE cars ADD INDEX idx_price (price)",
        "ALTER TABLE cars ADD INDEX idx_body_type (body_type)",
    ];

    foreach ($queries_indexes as $q) {
        try {
            $pdo->exec($q);
            echo "Индекс добавлен: $q\n";
        } catch (PDOException $e) {
            echo "Индекс уже существует или ошибка: " . $e->getMessage() . "\n";
        }
    }

    // 2. Таблица попыток входа (защита от брутфорса)
    $q_login_attempts = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempts INT DEFAULT 1,
        last_attempt_time DATETIME NOT NULL,
        UNIQUE KEY unique_ip (ip_address)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($q_login_attempts);
    echo "Таблица login_attempts создана/проверена.\n";

    // 3. Таблица избранного (закладки)
    $q_favorites = "CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT NOT NULL,
        id_car INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_fav (id_user, id_car),
        FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (id_car) REFERENCES cars(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($q_favorites);
    echo "Таблица favorites создана/проверена.\n";

    // 4. Таблица сброса паролей
    $q_password_resets = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        INDEX idx_email (email),
        INDEX idx_token (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($q_password_resets);
    echo "Таблица password_resets создана/проверена.\n";

    echo "\nМиграция базы данных успешно завершена!\n";

} catch (PDOException $e) {
    echo "Критическая ошибка БД: " . $e->getMessage() . "\n";
}
