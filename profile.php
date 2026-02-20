<?php
include "path.php";
include_once SITE_ROOT . "/app/database/db.php";
include_once SITE_ROOT . "/app/helps/csrf_helper.php";

// Требуем авторизации
if (!isset($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit;
}

$errMsg = [];
$successMsg = '';

$currentUser = selectOne('users', ['id' => $_SESSION['id']]);

// Переключение темы
if (isset($_POST['toggle_theme'])) {
    $newTheme = ($_SESSION['theme'] ?? 'light') === 'dark' ? 'light' : 'dark';
    $_SESSION['theme'] = $newTheme;
    header('location: ' . BASE_URL . 'profile.php');
    exit;
}

// Обновление профиля
if (isset($_POST['update_profile'])) {
    if (!validateCsrfToken($_POST)) {
        array_push($errMsg, "Ошибка CSRF токена. Попробуйте снова.");
    } else {
        $login = trim($_POST['login']);
        $email = trim($_POST['email']);
        $bio = trim($_POST['bio'] ?? '');

        if ($login === '' || $email === '') {
            array_push($errMsg, "Логин и Email не могут быть пустыми.");
        } else {
            $existEmail = selectOne('users', ['email' => $email]);
            $existLogin = selectOne('users', ['username' => $login]);

            if ($existEmail && $existEmail['id'] !== $_SESSION['id']) {
                array_push($errMsg, "Этот Email уже занят другим пользователем.");
            } elseif ($existLogin && $existLogin['id'] !== $_SESSION['id']) {
                array_push($errMsg, "Этот Логин уже занят.");
            } else {

                $updateData = [
                    'username' => $login,
                    'email' => $email,
                    'bio' => $bio
                ];

                // Проверка аватарки
                if (!empty($_POST['cropped_image'])) {
                    $base64_string = $_POST['cropped_image'];
                    $img_parts = explode(";base64,", $base64_string);
                    if (count($img_parts) == 2) {
                        $img_base64 = base64_decode($img_parts[1]);

                        $avatarName = time() . "_" . $_SESSION['id'] . ".webp";
                        $imgPath = ROOT_PATH . "/assets/images/avatars/" . $avatarName;

                        // Создаем папку если нет
                        if (!file_exists(ROOT_PATH . "/assets/images/avatars")) {
                            mkdir(ROOT_PATH . "/assets/images/avatars", 0777, true);
                        }

                        file_put_contents($imgPath, $img_base64);
                        $updateData['avatar'] = $avatarName;

                        // Удаление старой автарки
                        if (!empty($currentUser['avatar']) && file_exists(ROOT_PATH . "/assets/images/avatars/" . $currentUser['avatar'])) {
                            unlink(ROOT_PATH . "/assets/images/avatars/" . $currentUser['avatar']);
                        }
                    }
                }

                update('users', $_SESSION['id'], $updateData);
                $_SESSION['login'] = $login;
                $_SESSION['email'] = $email;
                $successMsg = "Данные профиля успешно обновлены!";
                // Обновляем текущего юзера после апдейта
                $currentUser = selectOne('users', ['id' => $_SESSION['id']]);
            }
        }
    }
}

// Смена пароля
if (isset($_POST['change_password'])) {
    if (!validateCsrfToken($_POST)) {
        array_push($errMsg, "Ошибка CSRF токена. Попробуйте снова.");
    } else {
        $oldPass = $_POST['old_password'];
        $newPass = $_POST['new_password'];
        $newPass2 = $_POST['new_password_2'];

        if (!password_verify($oldPass, $currentUser['password'])) {
            array_push($errMsg, "Текущий пароль введен неверно.");
        } elseif ($newPass !== $newPass2) {
            array_push($errMsg, "Новые пароли не совпадают.");
        } elseif (mb_strlen($newPass, 'UTF8') < 4) {
            array_push($errMsg, "Новый пароль должен содержать минимум 4 символа.");
        } else {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            update('users', $_SESSION['id'], ['password' => $hashed]);
            $successMsg = "Пароль успешно изменен!";
            $currentUser = selectOne('users', ['id' => $_SESSION['id']]);
        }
    }
}

// Получение избранных машин
global $pdo;
$stmt_fav = $pdo->prepare("
    SELECT c.*, b.name AS brand_name 
    FROM favorites f 
    JOIN cars c ON f.id_car = c.id 
    LEFT JOIN brands b ON c.id_brand = b.id 
    WHERE f.id_user = ? 
    ORDER BY f.created_at DESC
");
$stmt_fav->execute([$_SESSION['id']]);
$user_favorites_cars = $stmt_fav->fetchAll();

$userAvatar = !empty($currentUser['avatar']) ? BASE_URL . "assets/images/avatars/" . $currentUser['avatar'] : '';
?>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>Личный кабинет &mdash; ChinaCars</title>
    <style>
        .avatar-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary-color);
            background-color: var(--card-bg);
            cursor: pointer;
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-wrapper .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            opacity: 0;
            transition: 0.3s;
        }

        .avatar-wrapper:hover .overlay {
            opacity: 1;
        }
    </style>
</head>

<body class="<?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'dark-theme' : '' ?>">

    <?php include("app/include/header.php"); ?>

    <div class="container mt-5 mb-5">
        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="profile-card text-center p-4">
                    <div class="avatar-wrapper" onclick="document.getElementById('avatarInput').click();">
                        <?php if ($userAvatar): ?>
                            <img src="<?= $userAvatar ?>" id="avatarPreview" alt="Аватар">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 w-100">
                                <i class="fas fa-layer-group fa-4x text-muted" id="avatarIcon"></i>
                                <img src="" id="avatarPreview" alt="Аватар" style="display:none;">
                            </div>
                        <?php endif; ?>
                        <div class="overlay">
                            <i class="fas fa-camera fa-2x"></i>
                        </div>
                    </div>

                    <h4><?= htmlspecialchars($currentUser['username']) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($currentUser['email']) ?></p>

                    <div class="mb-3">
                        <?php if ($currentUser['admin']): ?>
                            <span class="badge bg-danger p-2"><i class="fas fa-crown"></i> Администратор</span>
                        <?php else: ?>
                            <span class="badge bg-primary p-2"><i class="fas fa-user"></i> Пользователь</span>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <h6 class="text-start mt-3 mb-3"><i class="fas fa-palette"></i> Оформление</h6>
                    <div class="theme-toggle-card mx-auto">
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
                        <?php if ($currentUser['admin']): ?>
                            <a href="<?= BASE_URL ?>admin/cars/index.php" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-cog"></i> Админ панель
                            </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt"></i> Выйти из аккаунта
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="profile-card p-4">

                    <?php if (!empty($errMsg)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errMsg as $err): ?>
                                    <li><?= $err ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($successMsg): ?>
                        <div class="alert alert-success">
                            <?= $successMsg ?>
                        </div>
                    <?php endif; ?>

                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main"
                                type="button" role="tab" aria-controls="main" aria-selected="true"><i
                                    class="fas fa-user"></i> Мой профиль</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security"
                                type="button" role="tab" aria-controls="security" aria-selected="false"><i
                                    class="fas fa-shield-alt"></i> Безопасность</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites"
                                type="button" role="tab" aria-controls="favorites" aria-selected="false"><i
                                    class="fas fa-heart"></i> Избранное</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Вкладка Мой профиль -->
                        <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="main-tab">
                            <form action="profile.php" method="post" id="profileForm">
                                <?= csrfField() ?>
                                <input type="file" id="avatarInput" accept="image/*" style="display:none;">
                                <input type="hidden" name="cropped_image" id="cropped_image">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ваш логин</label>
                                        <input type="text" name="login" class="form-control"
                                            value="<?= htmlspecialchars($currentUser['username']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email адрес</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">О себе (биография)</label>
                                        <textarea name="bio" class="form-control" rows="4"
                                            placeholder="Напишите немного о себе..."><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </form>
                        </div>

                        <!-- Вкладка Безопасность -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <form action="profile.php" method="post">
                                <?= csrfField() ?>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Текущий пароль</label>
                                        <div class="input-group">
                                            <input type="password" name="old_password" id="old_password"
                                                class="form-control" required
                                                placeholder="Введите ваш текущий пароль...">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="#old_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Новый пароль</label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" id="new_password"
                                                class="form-control" required placeholder="Минимум 4 символа...">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="#new_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Подтвердите новый пароль</label>
                                        <div class="input-group">
                                            <input type="password" name="new_password_2" id="new_password_2"
                                                class="form-control" required placeholder="Повторите пароль...">
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                data-target="#new_password_2">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Обновить пароль
                                </button>
                            </form>
                        </div>

                        <!-- Вкладка Избранное -->
                        <div class="tab-pane fade" id="favorites" role="tabpanel" aria-labelledby="favorites-tab">
                            <div class="row">
                                <?php if (count($user_favorites_cars) > 0): ?>
                                    <?php foreach ($user_favorites_cars as $fav): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100"
                                                style="background-color: var(--card-bg); border-color: var(--border-color);">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <a href="<?= BASE_URL ?>single.php?id=<?= $fav['id'] ?>"
                                                            class="text-decoration-none" style="color: var(--text-color);">
                                                            <?= htmlspecialchars($fav['brand_name'] . ' ' . $fav['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <p class="card-text text-muted mb-2">
                                                        <?= number_format($fav['price'], 0, '', ' ') ?>
                                                        ₽</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <a href="<?= BASE_URL ?>single.php?id=<?= $fav['id'] ?>"
                                                            class="btn btn-sm btn-outline-primary">Посмотреть авто</a>
                                                        <button class="fav-btn" data-id="<?= $fav['id'] ?>"
                                                            style="background:none; border:none; color:#ff4757; font-size:1.2rem;"
                                                            title="Убрать из избранного"><i class="fas fa-heart"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted">Вы пока ничего не добавили в закладки.</p>
                                        <a href="<?= BASE_URL ?>" class="btn btn-outline-primary mt-2">Перейти в каталог</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Модальное окно для обрезки -->
    <div class="modal fade" id="cropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background-color: var(--card-bg);">
                <div class="modal-header">
                    <h5 class="modal-title">Обрезка аватара</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="img-container"
                        style="max-height: 500px; width: 100%; display:flex; justify-content:center; background-color: #000;">
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="cropButton">Применить и сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <?php include("app/include/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Сохранение активной вкладки после перезагрузки
            const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (e) {
                    localStorage.setItem('activeProfileTab', e.target.id);
                });
            });

            const activeTab = localStorage.getItem('activeProfileTab');
            if (activeTab) {
                const tabBtn = document.getElementById(activeTab);
                if (tabBtn) {
                    const bsTab = new bootstrap.Tab(tabBtn);
                    bsTab.show();
                }
            }

            // Логика Cropper.js для аватара
            let cropper;
            const avatarInput = document.getElementById('avatarInput');
            const imageToCrop = document.getElementById('imageToCrop');
            const cropModalObj = new bootstrap.Modal(document.getElementById('cropModal'));
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarIcon = document.getElementById('avatarIcon');

            if (avatarInput) {
                avatarInput.addEventListener('change', function (e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        const file = files[0];
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            imageToCrop.src = event.target.result;
                            cropModalObj.show();
                        };
                        reader.readAsDataURL(file);
                        // очищаем input
                        avatarInput.value = '';
                    }
                });
            }

            const cropModalEl = document.getElementById('cropModal');
            if (cropModalEl) {
                cropModalEl.addEventListener('shown.bs.modal', function () {
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1, // Аватар квадратный
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true
                    });
                });

                cropModalEl.addEventListener('hidden.bs.modal', function () {
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                });
            }

            const cropBtn = document.getElementById('cropButton');
            if (cropBtn) {
                cropBtn.addEventListener('click', function () {
                    if (!cropper) return;

                    cropper.getCroppedCanvas({
                        width: 300,
                        height: 300
                    }).toBlob((blob) => {
                        const reader = new FileReader();
                        reader.onloadend = function () {
                            const base64data = reader.result;
                            document.getElementById('cropped_image').value = base64data;

                            // Показываем предпросмотр сразу
                            avatarPreview.src = base64data;
                            avatarPreview.style.display = 'block';
                            if (avatarIcon) avatarIcon.style.display = 'none';

                            cropModalObj.hide();

                            // Автоматически отправляем форму профиля
                            document.querySelector('button[name="update_profile"]').innerHTML = '<i class="fas fa-save"></i> Сохранить (с новым аватаром)';
                            document.querySelector('button[name="update_profile"]').classList.add('btn-warning');
                            document.querySelector('button[name="update_profile"]').classList.remove('btn-primary');
                        }
                        reader.readAsDataURL(blob);
                    }, 'image/webp', 0.8);
                });
            }
        });
    </script>
</body>

</html>