<?php
session_start();
require_once 'db_connect.php';

// Включение отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Проверка заполнения полей
    if (empty($login) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        // Поиск администратора в БД
        $stmt = $connection->prepare("SELECT * FROM admins WHERE login = ?");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        // Проверка пароля (без хеширования)
        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: admin_panel.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход для администратора</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .login-form { background: #f9f9f9; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 10px; }
        input { width: 100%; padding: 8px; margin: 5px 0; }
        button { background:rgb(63, 89, 175); color: white; padding: 10px; border: none; width: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Вход в панель администратора</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>