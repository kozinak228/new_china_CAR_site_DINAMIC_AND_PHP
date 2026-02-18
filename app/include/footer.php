<div class="footer container-fluid">
    <div class="footer-content container">
        <div class="row">
            <div class="footer-section about col-md-4 col-12">
                <h3 class="logo-text"><i class="fas fa-car"></i> ChinaCars</h3>
                <p>
                    ChinaCars — ваш надёжный помощник в подборе и покупке автомобилей из Китая.
                    Широкий каталог, подробные характеристики, честные цены.
                </p>
                <div class="contact">
                    <span><i class="fas fa-phone"></i> &nbsp; +7 (999) 123-45-67</span>
                    <span><i class="fas fa-envelope"></i> &nbsp; info@chinacars.ru</span>
                </div>
                <div class="socials">
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-section links col-md-4 col-12">
                <h3>Навигация</h3>
                <br>
                <ul>
                    <a href="<?php echo BASE_URL; ?>">
                        <li>Каталог</li>
                    </a>
                    <a href="<?php echo BASE_URL . 'gallery.php'; ?>">
                        <li>Галерея</li>
                    </a>
                    <a href="#">
                        <li>О компании</li>
                    </a>
                    <a href="#">
                        <li>Доставка</li>
                    </a>
                    <a href="#">
                        <li>Контакты</li>
                    </a>
                </ul>
            </div>

            <div class="footer-section contact-form col-md-4 col-12">
                <h3>Обратная связь</h3>
                <br>
                <form action="#" method="post">
                    <input type="email" name="email" class="text-input contact-input" placeholder="Ваш email...">
                    <textarea rows="4" name="message" class="text-input contact-input"
                        placeholder="Ваш вопрос..."></textarea>
                    <button type="submit" class="btn btn-big contact-btn">
                        <i class="fas fa-envelope"></i>
                        Отправить
                    </button>
                </form>
            </div>

        </div>

        <div class="footer-bottom">
            &copy; ChinaCars
            <?php echo date('Y'); ?> | Каталог автомобилей из Китая
        </div>
    </div>
</div>