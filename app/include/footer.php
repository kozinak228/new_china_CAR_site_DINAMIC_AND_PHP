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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const input = document.querySelector(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        });

        const favBtns = document.querySelectorAll('.fav-btn');
        favBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const carId = this.getAttribute('data-id');
                const icon = this.querySelector('i');

                fetch('<?= BASE_URL ?>ajax_favorites.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ car_id: carId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (data.action === 'added') {
                                icon.classList.remove('far');
                                icon.classList.add('fas');
                            } else {
                                icon.classList.remove('fas');
                                icon.classList.add('far');
                            }
                        } else {
                            if (data.message === 'Not authenticated') {
                                alert('Для добавления в избранное необходимо авторизоваться!');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>