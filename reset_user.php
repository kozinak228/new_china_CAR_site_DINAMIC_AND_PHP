<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";

$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("DELETE FROM users");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");

$pass = password_hash('danil123', PASSWORD_DEFAULT);
$pdo->exec("INSERT INTO users (id, username, email, password, admin) VALUES (1, 'danil', 'danil@chinacars.ru', '$pass', 1)");

// РћР±РЅРѕРІР»СЏРµРј cars С‡С‚РѕР±С‹ СЃСЃС‹Р»Р°Р»РёСЃСЊ РЅР° РЅРѕРІРѕРіРѕ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ
$pdo->exec("UPDATE cars SET id_user = 1");

$user = selectOne('users', ['username' => 'danil']);
echo "User: " . $user['username'] . "\n";
echo "Admin: " . $user['admin'] . "\n";
echo "Verify: " . (password_verify('danil123', $user['password']) ? 'OK' : 'FAIL') . "\n";
