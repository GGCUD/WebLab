<?php
// session.php
// Инициализация сессии и защита от XSS

// Устанавливаем безопасные настройки сессии
ini_set('session.use_strict_mode', 1);
session_start();

// Генерируем новый идентификатор сессии при логине для защиты от фиксации
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Функция для безопасного вывода
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}