<?php
require_once 'session.php';
require_once 'db_connect.php';
require_once 'error_handler.php';
include 'header.php';

$errorHandler = new ErrorHandler();
$registrationSuccess = false; 
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}
// Генерация CAPTCHA
if (!isset($_SESSION['captcha_answer'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_answer'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_num = preg_replace('/[^0-9]/', '', $_POST['phone_num'] ?? '');
    $passport = trim($_POST['passport'] ?? '');
    $mail = filter_var(trim($_POST['mail'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');

 // Валидация полей
    if (empty($full_name)) {
        $errorHandler->addError('full_name', 'Введите ФИО');
    }

    if (empty($phone_num) || !preg_match('/^9\d{9}$/', $phone_num)) {
        $errorHandler->addError('phone_num', 'Неверный формат телефона');
    }

    if (empty($passport)) {
        $errorHandler->addError('passport', 'Введите паспортные данные');
    }

    if ($mail && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errorHandler->addError('mail', 'Неверный формат email');
    }

    if (strlen($password) < 6) {
        $errorHandler->addError('password', 'Пароль должен быть не менее 6 символов');
    }

    if ($password !== $password_confirm) {
        $errorHandler->addError('password_confirm', 'Пароли не совпадают');
    }

    // Проверка CAPTCHA
    $user_answer = (int)($_POST['captcha'] ?? 0);
    if ($user_answer !== $_SESSION['captcha_answer']) {
        $errorHandler->addError('captcha', 'Неверный ответ на вопрос');
    }

    // Проверка уникальности
    if (!$errorHandler->hasErrors()) {
        try {
            $stmt = $connection->prepare("SELECT COUNT(*) FROM klient WHERE phone_num = ? OR mail = ?");
            $stmt->bind_param('ss', $phone_num, $mail);
            $stmt->execute();
            
            if ($stmt->get_result()->fetch_row()[0] > 0) {
                $errorHandler->addError('general', 'Пользователь уже существует');
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Database error: ".$e->getMessage());
            $errorHandler->addError('database', 'Ошибка базы данных');
        }
    }

    // Регистрация пользователя
    if (!$errorHandler->hasErrors()) {
        try {
            $stmt = $connection->prepare(
                "INSERT INTO klient 
                (full_name, address, phone_num, passport, mail, password) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            $stmt->bind_param('ssssss', 
                $full_name, 
                $address, 
                $phone_num, 
                $passport, 
                $mail, 
                $password
            );

            if ($stmt->execute()) {
                $registrationSuccess = true;
                 $_SESSION['captcha_answer'] = null;
                //exit;
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Registration error: ".$e->getMessage());
            $errorHandler->addError('database', 'Ошибка регистрации');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация клиента</title>

</head>
<body>

<div class="register-wrapper">
    <?php if ($registrationSuccess): ?>
        <div class="success-message">
            <h3>✅ Регистрация прошла успешно!</h3>
            <p>Теперь вы можете войти в свой аккаунт.</p>
            <a href="login.php" class="auth-button">Перейти к авторизации</a>
        </div>
    <?php else: ?>
    <div class="register-card">
        <h2 class="form-title">Регистрация клиента</h2>

        <?php if ($errorHandler->hasErrors()): ?>
            <div class="alert">
                <ul>
                    <?php foreach ($errorHandler->getErrors() as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="form-vertical">
            <div class="form-group">
                <input type="text" 
                    name="full_name" 
                    class="form-input <?= $errorHandler->getError('full_name') ? 'error' : '' ?>" 
                    placeholder="ФИО"
                    value="<?= e($_POST['full_name'] ?? '') ?>"
                    required>
                <?php if ($errorHandler->getError('full_name')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('full_name')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="text" 
                    name="address" 
                    class="form-input" 
                    placeholder="Адрес"
                    value="<?= e($_POST['address'] ?? '') ?>">
            </div>

            <div class="form-group">
                <input type="text" 
                    name="phone_num" 
                    class="form-input <?= $errorHandler->getError('phone_num') ? 'error' : '' ?>" 
                    placeholder="Телефон (9XXXXXXXXX)"
                    value="<?= e($_POST['phone_num'] ?? '') ?>"
                    required>
                <?php if ($errorHandler->getError('phone_num')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('phone_num')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="text" 
                    name="passport" 
                    class="form-input <?= $errorHandler->getError('passport') ? 'error' : '' ?>" 
                    placeholder="Паспортные данные"
                    value="<?= e($_POST['passport'] ?? '') ?>"
                    required>
                <?php if ($errorHandler->getError('passport')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('passport')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="email" 
                    name="mail" 
                    class="form-input <?= $errorHandler->getError('mail') ? 'error' : '' ?>" 
                    placeholder="Email"
                    value="<?= e($_POST['mail'] ?? '') ?>">
                <?php if ($errorHandler->getError('mail')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('mail')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" 
                    name="password" 
                    class="form-input <?= $errorHandler->getError('password') ? 'error' : '' ?>" 
                    placeholder="Пароль"
                    required>
                <?php if ($errorHandler->getError('password')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('password')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" 
                    name="password_confirm" 
                    class="form-input <?= $errorHandler->getError('password_confirm') ? 'error' : '' ?>" 
                    placeholder="Повторите пароль"
                    required>
                <?php if ($errorHandler->getError('password_confirm')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('password_confirm')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Решите пример: <?= $_SESSION['captcha_question'] ?> = ?</label>
                <input type="number" 
                    name="captcha" 
                    class="form-input <?= $errorHandler->getError('captcha') ? 'error' : '' ?>" 
                    required>
                <?php if ($errorHandler->getError('captcha')): ?>
                    <span class="error-msg"><?= e($errorHandler->getError('captcha')) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
    <?php endif; ?>
</div>
    <style>
       
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            color: #2d3748;
        }

        .register-wrapper {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .register-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .form-title {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: #2b6cb0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            border-color: #3182ce;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .form-input.error {
            border-color: #e53e3e;
        }

        .error-msg {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .captcha-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .captcha-img {
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            padding: 0.5rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #2b6cb0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #2c5282;
        }

        .alert {
            background-color: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
                .success-message {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin: 2rem auto;
            max-width: 600px;
        }

        .auth-button {
            display: inline-block;
            margin-top: 1rem;
            text-decoration: none;
            background: #2b6cb0;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .auth-button:hover {
            background: #2c5282;
        }
    </style>
<?php include 'footer.php'; ?>
</body>
</html>
