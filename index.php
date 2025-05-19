<?php
session_start();
require_once 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Юридическое агентство</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php';

?>

<main>
    <section class="intro">
        <h2>О нашем агентстве</h2>
        <p>
            Добро пожаловать в <strong>Юридическое агентство</strong> — ваш надежный партнер в мире права. 
            Мы специализируемся на предоставлении полного спектра юридических услуг:
            консультаций, составлении договоров, сопровождении бизнеса, защите интересов в суде и многом другом.
        </p>
        <p>
            Наша команда опытных юристов гарантирует индивидуальный подход к каждому клиенту,
            соблюдение конфиденциальности и оперативность решения любых правовых вопросов.
        </p>
    </section>
</main>

<?php
// Подключаем подвал (footer.php) — в нём закрываются main, <body> и <html>
include 'footer.php';
?>