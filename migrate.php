<?php
include "path.php";
include SITE_ROOT . "/app/database/db.php";

echo "<h3>Миграция: добавление колонки featured</h3>";

try {
    $pdo->exec("ALTER TABLE cars ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0");
    echo "<p style='color:green;'>✅ Колонка <b>featured</b> успешно добавлена в таблицу <b>cars</b>.</p>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "<p style='color:orange;'>⚠️ Колонка <b>featured</b> уже существует.</p>";
    } else {
        echo "<p style='color:red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
    }
}

echo "<br><h3>Миграция: обновление учётных данных админа</h3>";

try {
    $stmt = $pdo->prepare("UPDATE users SET username = :newname, email = :newemail WHERE username = :oldname AND admin = 1");
    $stmt->execute([
        'newname' => 'danil@gmail.com',
        'newemail' => 'danil@gmail.com',
        'oldname' => 'danil'
    ]);
    $affected = $stmt->rowCount();
    if ($affected > 0) {
        echo "<p style='color:green;'>✅ Логин админа изменён на <b>danil@gmail.com</b> (пароль: danil123).</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ Админ 'danil' не найден или уже обновлён.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>← Вернуться на главную</a>";
