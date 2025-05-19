<?php
// auth.php
// Проверка авторизации пользователя

require_once __DIR__ . '/session.php';  // Инициализируем сессию и функцию e()

// Если пользователь не залогинен — перенаправляем на страницу входа
if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Опционально: можно загрузить данные текущего клиента
require_once __DIR__ . '/db_connect.php';

$stmt = $connection->prepare('SELECT id_klient, full_name, address, phone_num, passport, mail FROM klient WHERE id_klient = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Сессия некорректна — выходим
    session_destroy();
    header('Location: /login.php');
    exit;
}
$current_user = $result->fetch_assoc();