<?php
session_start();
require_once 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <!--<title>Юридическое агентство</title>-->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<div class="container">
    
                <section class="about-us">
            <div class="about-us-content">
            <div class="about-us-text">
            <h2>О нас</h2>
            <p>Юридическая компания основана в 2014г. Занимается оказанием юридической помощи гражданам, представлением интересов физических лиц в судах.</p>
            <p>Юристы компании разрешают вопросы семейных, жилищных, наследственных споров и др. областей права. Представление интересов граждан в судах осуществляется по всей России. Наша география судебной практики на сегодня в 15 регионах страны, 27 городах России.</p>
            <p>Основатель юридической компании - Наталья Николаевна Ковалевская.</p>
            <p>Начиналось «дело» с основателя компании, которая смогла себя зарекомендовать в качестве профессионала и опытного юриста, завоевать доверие через свое отношение к клиентам и подход к разрешению их вопросов. В результате это позволило расширить штат компании.</p>
            <p>В юридической компании «Наталья Ковалевская и партнёры» работают квалифицированные юристы со стажем работы более 20 лет. Компания направлена на оказание профессиональной юридической помощи физическим и юридическим лицам.</p>
            <p>Коллектив компании устоялся с 2017 года и успешно продолжает работу в дружном сплоченном составе.</p>
            <p>Одним из главных правил компании является сопровождение клиента именно тем юристом, с которым клиент изначально начал работу.</p>
            </div>
        </div>
        </section>
        <section class="facts-block">
        <h2 class="facts-title">ФАКТЫ</h2>
        <div class="facts-grid">
            <div class="fact-item"><span>с 2014</span> Основана юридическая компания</div>
            <div class="fact-item"><span>с 2004</span> У основателя юридического стажа</div>
            <div class="fact-item"><span>свыше 934</span> выигранных дел</div>
            <div class="fact-item"><span>свыше 9867</span> составлено документов</div>
            <div class="fact-item"><span>свыше 17228</span> Юристы компании представили интересы клиентов в судах</div>
            <div class="fact-item"><span>свыше 10000</span> Консультаций проведено успешно</div>
        </div>
        </section>

    <!-- Слайдер сертификатов -->   
    <section class="certificates-slider">
        <h2 class="slider-title">Наши сертификаты и лицензии</h2>
  <div class="certificates-container" style="position: relative; overflow: hidden; border-radius: 8px;">
            <div class="certificates-track" id="slider-track">
                <div class="certificate-item">
                    <img src="images/___.jpg" alt="Сертификат 1">
                </div>
                <div class="certificate-item">
                    <img src="images/__22717.jpg.webp" alt="Сертификат 2">
                </div>
                <div class="certificate-item">
                    <img src="images/6a4d82637d72ebb6f70e.jpg.webp" alt="Сертификат 3">
                </div>
                <div class="certificate-item">
                    <img src="images/certificate.jpg.webp" alt="Сертификат 4">
                </div>
                <div class="certificate-item">
                    <img src="images/doc00175820240603165.png" alt="Сертификат 5">
                </div>
                <div class="certificate-item">
                    <img src="images/photo.jpg" alt="Сертификат 6">
                </div>
            </div>
        </div>
        <div class="slider-controls">
            <button class="slider-btn" onclick="moveSlide(-1)">‹ Назад</button>
            <button class="slider-btn" onclick="moveSlide(1)">Вперёд ›</button>
        </div>
    </section>

    <!-- Контактная информация и карта -->
    <section>
        <h2 class="contacts-title">Как нас найти</h2>
        <p class="contacts-description">
            Наш офис расположен в удобном месте города. Вы можете посетить нас в будние дни с 9:00 до 18:00.
        </p>

        <div class="contacts-section">
            <div class="contacts-info">
                <h3>Контакты</h3>
                <ul class="contacts-list">
                    <li>
                        <img src="images/map-icon.svg" alt="Адрес" class="contact-icon">
                        Санкт-Петербург, Невский пр-т, д. 100
                    </li>
                    <li>
                        <img src="images/phone-icon.svg" alt="Телефон" class="contact-icon">
                        <a href="tel:+78121234567">+7 (812) 123-45-67</a>
                    </li>
                    <li>
                        <img src="images/email-icon.svg" alt="Email" class="contact-icon">
                        <a href="mailto:info@lawagency.ru">info@lawagency.ru</a>
                    </li>
                    <li>
                        <img src="images/clock-icon.svg" alt="Часы работы" class="contact-icon">
                        Пн–Пт: 9:00 – 18:00<br>Сб–Вс: выходной
                    </li>
                </ul>
            </div>

            <div class="contacts-map">
            <script 
            type="text/javascript" charset="utf-8" 
            async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A54322cdccd4da6e3ef4cb5725c3ac2017efa3e3a7267c0e3d46e65978a1135bc&amp;width=924&amp;height=663&amp;lang=ru_RU&amp;scroll=true">

            </script>
            </div>
        </div>
    </section>
            <section class="contact-form-section" style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h2 style="text-align:center; color:#2b6cb0;">Обратная связь</h2>
            <form id="contactForm" method="POST" action="send_mail.php">
                <label for="name">Имя:</label><br>
                <input type="text" id="name" name="name" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

                <label for="message">Сообщение:</label><br>
                <textarea id="message" name="message" required rows="5" style="width: 100%; padding: 8px; margin-bottom: 15px;"></textarea><br>

                <button type="submit" style="background: #2b6cb0; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Отправить</button>
            </form>
            <div id="formResponse" style="margin-top: 15px; color: red;"></div>
        </section>

        <script>
        document.getElementById('contactForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const responseDiv = document.getElementById('formResponse');

            responseDiv.textContent = 'Отправка...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                responseDiv.style.color = response.ok ? 'green' : 'red';
                responseDiv.textContent = text;
                if (response.ok) form.reset();
            } catch (e) {
                responseDiv.style.color = 'red';
                responseDiv.textContent = 'Ошибка отправки. Попробуйте позже.';
            }
        });
        </script>
    
    </div>
<?php include 'footer.php'; ?>
<script>
    // Простой JS-слайдер
    let currentIndex = 0;
    const track = document.getElementById('slider-track');
    const totalSlides = track.children.length;

    function moveSlide(direction) {
        currentIndex += direction;
        if (currentIndex < 0) currentIndex = totalSlides - 1;
        if (currentIndex >= totalSlides) currentIndex = 0;
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
</script>

</body>

<style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        box-sizing: border-box;
        }

        .certificates-slider {
             position: relative;
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            position: relative;
        }
        .slider-controls {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            pointer-events: none; 
            display: flex;
            justify-content: space-between;
        }

        .slider-title {
            text-align: center;
            font-size: 24px;
            color: #2b6cb0;
            margin-bottom: 20px;
        }

        .certificates-container {
            overflow: hidden;
            border-radius: 8px;
        }

        .certificates-track {
            display: flex;
            transition: transform 0.5s ease;
        }

        .certificate-item {
            flex: 0 0 100%;
            box-sizing: border-box;
            padding: 10px;
        }

        .certificate-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .slider-controls {
            text-align: center;
            margin-top: 10px;
        }

        .slider-btn {
        pointer-events: all;
        background: rgba(255, 255, 255, 0.12); 
        border: none;
        width: 12%; 
        height: 100%;
        cursor: pointer;
        position: relative;
        color: transparent; 
        font-size: 0;
        transition: background 0.3s ease;
        }

        .slider-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }


        .slider-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: solid #2b6cb0;
            border-width: 0 3px 3px 0;
            padding: 8px;
            opacity: 0.7;
        }

        .contacts-section {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .contacts-info {
            flex: 1;
            min-width: 300px;
        }

        .contacts-map {
            flex: 2;
            min-width: 300px;
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .contacts-list {
            list-style: none;
            padding: 0;
        }

        .contacts-list li {
            margin-bottom: 20px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .contact-icon {
            width: 24px;
            height: 24px;
        }

        .contacts-title {
            font-size: 2rem;
            color: #2b6cb0;
            margin: 2rem auto 1rem;
            text-align: center;
        }

        .contacts-description {
            text-align: center;
            margin-bottom: 2rem;
            color: #4a5568;
        }

        .map-container {
            width: 100%;
            height: 500px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .facts-block {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Segoe UI', sans-serif;
        color: #333;
        text-align: center;
        }

        .facts-title {
        font-size: 2.5rem;
        color: #2b6cb0;
        margin-bottom: 30px;
        font-weight: 700;
        letter-spacing: 2px;
        }

        .facts-grid {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        }

        .fact-item {
        flex: 1;
        font-size: 18px;
        line-height: 1.5;
        text-align: left;
        }

        .fact-item span {
        color: #2b6cb0;
        font-weight: 700;
        display: block;   
        margin-bottom: 4px; 
        font-size: 1.1em; 
        }
            .about-us {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px 40px;
        background: #e8f0fe;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(43, 108, 176, 0.2);
        }

        .about-us-content {
        display: flex;
        align-items: flex-start;
        gap: 40px;
        flex-wrap: wrap;
        }

        .about-us-image {
        flex: 1 1 350px;
        text-align: center;
        }

        .about-us-image img {
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(43, 108, 176, 0.3);
        object-fit: cover;
        height: 100%;
        }

        .about-us-text {
        flex: 2 1 600px;
        color: #1a365d;
        font-size: 1.1rem;
        line-height: 1.6;
        }

        .about-us-text h2 {
        font-size: 2.2rem;
        color: #2b6cb0;
        margin-bottom: 20px;
        }

        .about-us-text p {
        margin-bottom: 15px;
        }
</style>
</html>
