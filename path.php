<?php
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', __DIR__);
    $is_local = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', '127.0.0.1']);
    define('BASE_URL', $is_local ? "http://localhost:8000/" : "https://avtotachka.ru/");
    define('ROOT_PATH', realpath(dirname(__FILE__)));
}