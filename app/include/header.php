<header class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12">
                <h1>
                    <a href="<?php echo BASE_URL ?>"><i class="fas fa-car"></i> ChinaCars</a>
                </h1>
            </div>
            <div class="col-lg-5 col-md-4 col-12 d-flex align-items-center">
                <div class="search-container w-100 mt-2 mt-md-0">
                    <form action="<?= BASE_URL ?>search.php" method="post" id="headerSearchForm" autocomplete="off">
                        <div class="input-group">
                            <input type="text" name="search-term" id="headerSearchInput" class="form-control"
                                placeholder="Поиск авто (марка, модель...)"
                                style="border-radius: 20px 0 0 20px; border-right: none;">
                            <button class="btn btn-outline-light" type="submit"
                                style="border-radius: 0 20px 20px 0; border-left: none; background: rgba(255,255,255,0.1);">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div id="search-results"></div>
                </div>
            </div>
            <nav class="col-lg-4 col-md-4 col-12">
                <ul>
                    <li><a href="<?php echo BASE_URL ?>">Каталог</a></li>
                    <li><a href="<?php echo BASE_URL . 'gallery.php' ?>">Галерея</a></li>

                    <li>
                        <?php if (isset($_SESSION['id'])): ?>
                            <a href="#">
                                <i class="fa fa-user"></i>
                                <?php echo $_SESSION['login']; ?>
                            </a>
                            <ul>
                                <?php if ($_SESSION['admin']): ?>
                                    <li><a href="<?php echo BASE_URL . 'admin/cars/index.php'; ?>">Админ панель</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo BASE_URL . 'profile.php'; ?>"><i class="fas fa-palette"></i> Личный
                                        кабинет</a></li>
                                <li><a href="<?php echo BASE_URL . "logout.php"; ?>">Выход</a></li>
                            </ul>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL . "log.php"; ?>">
                                <i class="fa fa-user"></i>
                                Войти
                            </a>
                            <ul>
                                <li><a href="<?php echo BASE_URL . "reg.php"; ?>">Регистрация</a></li>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>