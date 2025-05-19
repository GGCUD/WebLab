<?php
// db_connect.php
// Подключение к MySQL

// Параметры подключения — подставьте свои
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';      // или ваш пароль
$db_name = 'test';  // имя вашей БД из test.sql

// Создаём объект подключения
$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Проверяем подключение
if ($connection->connect_error) {
    die('Ошибка подключения к БД: ' . $connection->connect_error);
}

// Устанавливаем кодировку UTF‑8
$connection->set_charset('utf8mb4');