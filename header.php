<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Юридическое агентство</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">Юридическое агентство</div>
    <nav>
        <a href="index.php">Главная</a>
        <a href="gallery.php">Галерея</a>
        <a href="services.php">Услуги</a>
        <!--<a href="contacts.php">Контакты</a>
        <a href="guestbook.php">Гостевая книга</a>-->
        <?php if(isset($_SESSION['user'])): ?>
            <a href="profile.php">Кабинет</a>
            <a href="logout.php">Выход</a>
        <?php else: ?>
            <a href="register.php">Регистрация</a>
            <a href="login.php">Авторизация</a>
        <?php endif; ?>
    </nav>
</header>
<main>
