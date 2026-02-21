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
    // AJAX LIVE SEARCH
    const searchInput = document.getElementById('headerSearchInput');
    const searchResults = document.getElementById('search-results');
    let debounceTimer;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim();
            clearTimeout(debounceTimer);

            if (term.length < 2) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch('<?= BASE_URL ?>ajax_search.php?term=' + encodeURIComponent(term))
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.items && data.items.length > 0) {
                            data.items.forEach(car => {
                                const item = document.createElement('a');
                                item.href = car.url;
                                item.className = 'search-result-item';

                                const imgHTML = car.img
                                    ? `<img src="${car.img}" class="search-result-img" alt="">`
                                    : `<div class="search-result-img d-flex align-items-center justify-content-center"><i class="fas fa-car text-muted"></i></div>`;

                                item.innerHTML = `
                                    ${imgHTML}
                                    <div class="search-result-info">
                                        <span class="search-result-title">${car.title}</span>
                                        <span class="search-result-meta">${car.brand}</span>
                                    </div>
                                    <div class="search-result-price">${car.price}</div>
                                `;
                                searchResults.appendChild(item);
                            });

                            if (data.total > data.items.length) {
                                const allLink = document.createElement('a');
                                allLink.href = '#';
                                allLink.className = 'search-result-item justify-content-center fw-bold';
                                allLink.style.color = '#e94560';
                                allLink.innerHTML = `Показать все результаты (${data.total})`;
                                allLink.onclick = (e) => {
                                    e.preventDefault();
                                    document.getElementById('headerSearchForm').submit();
                                };
                                searchResults.appendChild(allLink);
                            }
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="search-no-results">Ничего не найдено</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });
            }, 300); // 300ms debounce
        });

        // Закрытие при клике вне области
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Показ при фокусе, если есть текст
        searchInput.addEventListener('focus', function () {
            if (this.value.trim().length >= 2 && searchResults.innerHTML !== '') {
                searchResults.style.display = 'block';
            }
        });
    }
</script>