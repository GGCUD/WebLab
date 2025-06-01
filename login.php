<?php
// login.php — авторизация клиента
require_once 'session.php';
require_once 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_num = trim($_POST['phone_num'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Валидация
    if (empty($phone_num)) $errors[] = 'Введите номер телефона';
    if (empty($password)) $errors[] = 'Введите пароль';

    // Проверка пользователя
    if (empty($errors)) {
        $stmt = $connection->prepare('SELECT id_klient, password FROM klient WHERE phone_num = ?');
        $stmt->bind_param('s', $phone_num);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $db_password);
            $stmt->fetch();

            if ($password === $db_password) {
                $_SESSION['user_id'] = $id;
                header('Location: profile.php');
                exit;
            } else {
                $errors[] = 'Неверный пароль';
            }
        } else {
            $errors[] = 'Пользователь не найден';
        }
        $stmt->close();
    }
}
?>

<?php include 'header.php'; ?>

<main class="main-content">
        <?php if (isset($_GET['registered'])): ?>
        <div class="alert success" style="background:rgb(156, 191, 236); color:rgb(34, 76, 84); border-color:rgb(154, 221, 230);">
            Регистрация прошла успешно! Войдите в систему.
        </div>
    <?php endif; ?>
    <style>
        .alert.success {
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
        }
        /* Auth Section */
            .auth-section {
                padding: 80px 0;
                background: #f8fafc;
            }

            .auth-container {
                max-width: 500px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }

            .section-title {
                color: #2b6cb0;
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
                text-align: center;
            }

            .auth-form {
                display: grid;
                gap: 1.5rem;
            }

            .form-group {
                display: grid;
                gap: 0.5rem;
            }

            .form-label {
                font-weight: 500;
                color: #2d3748;
            }

            .form-input {
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #cbd5e0;
                border-radius: 6px;
                font-size: 1rem;
                transition: border-color 0.3s ease;
            }

            .form-input:focus {
                border-color: #3182ce;
                outline: none;
            }

            .form-actions {
                margin-top: 1rem;
                text-align: center;
            }

            .auth-button {
                background: #2b6cb0;
                color: white;
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 6px;
                font-size: 1rem;
                cursor: pointer;
                transition: background 0.3s ease;
                width: 100%;
            }

            .auth-button:hover {
                background: #2c5282;
            }

            .errors-list {
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .errors-list li {
                color: #742a2a;
                font-size: 0.9rem;
                padding: 5px 0;
            }

            .alert {
                background-color: #fed7d7;
                color: #742a2a;
                border: 1px solid #feb2b2;
                padding: 1rem;
                border-radius: 6px;
                margin-bottom: 1.5rem;
            }
    </style>
    <section class="section auth-section">
        <div class="section-content">
            <div class="auth-container">
                <h2 class="section-title">Вход в личный кабинет</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert error">
                        <ul class="errors-list">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="auth-form">
                    <div class="form-group">
                        <label for="phone_num" class="form-label">Телефон</label>
                        <input 
                            type="tel" 
                            id="phone_num" 
                            name="phone_num" 
                            class="form-input"
                            value="<?= htmlspecialchars($_POST['phone_num'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Пароль</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input"
                            placeholder="Введите пароль" 
                            required
                        >
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="auth-button">Войти</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
