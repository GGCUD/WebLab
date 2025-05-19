<?php
// register.php — регистрация клиента
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Считываем данные из формы
    $full_name = trim($_POST['full_name'] ?? '');
    $address   = trim($_POST['address']   ?? '');
    $phone_num = trim($_POST['phone_num'] ?? '');
    $passport  = trim($_POST['passport']  ?? '');
    $mail      = trim($_POST['mail']      ?? '');
    $password  = $_POST['password']      ?? '';

    // Валидация
    if ($full_name === '') {
        $errors[] = 'Введите ФИО.';
    }
    if ($phone_num === '') {
        $errors[] = 'Введите номер телефона.';
    }
    if ($passport === '') {
        $errors[] = 'Введите паспортные данные.';
    }
    if ($mail !== '' && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат e-mail.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов.';
    }

    // Проверка уникальности (телефон или e-mail)
    if (empty($errors)) {
        $stmt = $connection->prepare("SELECT COUNT(*) FROM klient WHERE phone_num = ? OR mail = ?");
        $stmt->bind_param('ss', $phone_num, $mail);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $errors[] = 'Клиент с таким телефоном или e-mail уже зарегистрирован.';
        }
    }

    // Если ошибок нет — сохраняем
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare(
            "INSERT INTO klient
             (full_name, address, phone_num, passport, password, mail)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssssss', $full_name, $address, $phone_num, $passport, $hash, $mail);
        $stmt->execute();

        // Автовход
        $_SESSION['user_id'] = $stmt->insert_id;
        header('Location: /profile.php');
        exit;
    }
}
?>

<?php include __DIR__ . '/templates/header.php'; ?>
<h2>Регистрация</h2>

<?php if (!empty($errors)): ?>
  <ul class="errors">
    <?php foreach ($errors as $err): ?>
      <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="post">
  <input type="text"    name="full_name" placeholder="ФИО"       value="<?= htmlspecialchars($full_name ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
  <input type="text"    name="address"   placeholder="Адрес"    value="<?= htmlspecialchars($address   ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <input type="text"    name="phone_num" placeholder="Телефон"  value="<?= htmlspecialchars($phone_num ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
  <input type="text"    name="passport"  placeholder="Паспорт"  value="<?= htmlspecialchars($passport  ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
  <input type="email"   name="mail"      placeholder="Email"    value="<?= htmlspecialchars($mail      ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <input type="password" name="password" placeholder="Пароль"  required>
  <button type="submit">Зарегистрироваться</button>
</form>

<?php include __DIR__ . '/templates/footer.php'; ?>
