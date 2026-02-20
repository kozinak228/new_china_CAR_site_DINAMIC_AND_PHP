<?php include("path.php");
include "app/controllers/users.php";
?>
<html lang="ru">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Custom Styling -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>My blog</title>
</head>

<body class="<?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'dark-theme' : '' ?>">

    <?php include("app/include/header.php"); ?>

    <!-- END HEADER -->
    <!-- FORM -->
    <div class="container reg_form">
        <form class="row justify-content-center" method="post" action="log.php">
            <h2 class="col-12">Авторизация</h2>
            <div class="mb-3 col-12 col-md-4 err" id="errorBlock">
                <?php
                $blockedTimer = 0;
                if (!empty($errMsg)):
                    foreach ($errMsg as $e):
                        if (strpos($e, 'BLOCKED_TIMER:') === 0) {
                            $blockedTimer = (int) str_replace('BLOCKED_TIMER:', '', $e);
                            echo "<p id='blockMessage' class='text-danger fw-bold'>Слишком много попыток входа.</p>";
                        } else {
                            echo "<p>$e</p>";
                        }
                    endforeach;
                endif;
                ?>
            </div>
            <?= csrfField() ?>
            <div class="w-100"></div>
            <div class="mb-3 col-12 col-md-4">
                <label for="formGroupExampleInput" class="form-label">Email</label>
                <input name="mail" value="<?= $email ?>" type="email" class="form-control" id="exampleInputEmail1"
                    placeholder="example@gmail.com">
            </div>
            <div class="w-100"></div>
            <div class="mb-3 col-12 col-md-4">
                <label for="exampleInputPassword1" class="form-label">Пароль</label>
                <div class="input-group">
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1"
                        placeholder="введите ваш пароль...">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                        data-target="#exampleInputPassword1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="w-100"></div>
            <div class="mb-3 col-12 col-md-4">
                <button type="submit" name="button-log" id="loginBtn"
                    class="btn btn-secondary w-100 mb-2">Войти</button>
                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>reg.php">Зарегистрироваться</a>
                    <a href="<?= BASE_URL ?>forgot.php" class="text-muted">Забыли пароль?</a>
                </div>
            </div>
        </form>
    </div>
    <!-- END FORM -->

    <!-- footer -->
    <?php include("app/include/footer.php"); ?>
    <!-- // footer -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timeLeft = <?= isset($blockedTimer) ? $blockedTimer : 0 ?>;
        const msgBlock = document.getElementById('blockMessage');
        const loginBtn = document.getElementById('loginBtn');
        const inputs = document.querySelectorAll('#exampleInputEmail1, #exampleInputPassword1');

        if (timeLeft > 0) {
            // Блокируем форму
            loginBtn.disabled = true;
            inputs.forEach(input => input.disabled = true);

            const formatTime = (seconds) => {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s < 10 ? '0' : ''}${s}`;
            };

            const timerText = document.createElement('span');
            msgBlock.appendChild(document.createElement('br'));
            msgBlock.appendChild(timerText);

            const timerId = setInterval(() => {
                timerText.innerHTML = `Попробуйте снова через: <b>${formatTime(timeLeft)}</b>`;
                if (timeLeft <= 0) {
                    clearInterval(timerId);
                    msgBlock.innerHTML = "Блокировка снята. Вы можете войти.";
                    msgBlock.classList.remove('text-danger');
                    msgBlock.classList.add('text-success');
                    loginBtn.disabled = false;
                    inputs.forEach(input => input.disabled = false);
                }
                timeLeft--;
            }, 1000);
        }
    </script>
</body>

</html>