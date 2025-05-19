<?php
// login.php — авторизация клиента
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_num = trim($_POST['phone_num'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Простая валидация наличия
    if ($phone_num === '') {
        $errors[] = 'Введите номер телефона.';
    }
    if ($password === '') {
        $errors[] = 'Введите пароль.';
    }

    // Проверяем пользователя в БД
    if (empty($errors)) {
        $stmt = $connection->prepare(
            'SELECT id_klient, password FROM klient WHERE phone_num = ?'
        );
        $stmt->bind_param('s', $phone_num);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                // Успешная авторизация
                $_SESSION['user_id'] = $id;
                header('Location: /profile.php');
                exit;
            } else {
                $errors[] = 'Неверный пароль.';
            }
        } else {
            $errors[] = 'Пользователь не найден.';
        }
        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/templates/header.php'; ?>
<h2>Вход</h2>

<?php if (!empty($errors)): ?>
  <ul class="errors">
    <?php foreach ($errors as $err): ?>
      <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="post">
  <input type="text"    name="phone_num" placeholder="Телефон" value="<?= htmlspecialchars($phone_num ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
  <input type="password" name="password"  placeholder="Пароль" required>
  <button type="submit">Войти</button>
</form>

<?php include __DIR__ . '/templates/footer.php'; ?>
