<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Генерирует CSRF-токен и сохраняет его в сессии
 * @return string
 */
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Проверяет CSRF-токен из массива данных (обычно $_POST)
 * @param array $data
 * @return bool
 */
function validateCsrfToken($data)
{
    if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $data['csrf_token']);
}

/**
 * Выводит скрытое поле с CSRF-токеном для вставки в форму
 * @return string
 */
function csrfField()
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
