<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";

// Требуем авторизации
if (!isset($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit;
}

// Переключение темы
if (isset($_POST['toggle_theme'])) {
    $newTheme = ($_SESSION['theme'] ?? 'light') === 'dark' ? 'light' : 'dark';
    $_SESSION['theme'] = $newTheme;
    header('location: ' . BASE_URL . 'profile.php');
    exit;
}
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
    <title>Личный кабинет — ChinaCars</title>
</head>

<body class="<?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'dark-theme' : '' ?>">

    <?php include("app/include/header.php"); ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-12">
                <div class="profile-card">
                    <h2><i class="fas fa-user-circle"></i> Личный кабинет</h2>
                    <hr>

                    <div class="profile-info mb-4">
                        <div class="profile-row">
                            <span class="profile-label"><i class="fas fa-user"></i> Логин</span>
                            <span class="profile-value">
                                <?= $_SESSION['login'] ?>
                            </span>
                        </div>
                        <div class="profile-row">
                            <span class="profile-label"><i class="fas fa-shield-alt"></i> Роль</span>
                            <span class="profile-value">
                                <?php if ($_SESSION['admin']): ?>
                                    <span class="badge-admin">Администратор</span>
                                <?php else: ?>
                                    <span class="badge-user">Пользователь</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <h5 class="mb-3"><i class="fas fa-palette"></i> Оформление</h5>
                    <div class="theme-toggle-card">
                        <div class="theme-toggle-label">
                            <i class="fas <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'fa-moon' : 'fa-sun' ?>"></i>
                            <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'Тёмная тема' : 'Светлая тема' ?>
                        </div>
                        <form method="post" action="profile.php">
                            <button type="submit" name="toggle_theme"
                                class="btn-theme-toggle <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'is-dark' : '' ?>">
                                <span class="toggle-circle"></span>
                            </button>
                        </form>
                    </div>

                    <div class="mt-4">
                        <?php if ($_SESSION['admin']): ?>
                            <a href="<?= BASE_URL ?>admin/cars/index.php" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-cog"></i> Админ панель
                            </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Выйти
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("app/include/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>