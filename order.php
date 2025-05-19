<?php
// order.php — оформление заявки
require_once __DIR__ . '/includes/auth.php';      // старт сессии, проверка авторизации, $current_user
require_once __DIR__ . '/includes/db_connect.php';

$user_id = $current_user['id_klient'];
$errors = [];

// Получаем все специализации для фильтра (опционально)
$specRes = $connection->query('SELECT id_specialization, name FROM specialization');
$specializations = $specRes->fetch_all(MYSQLI_ASSOC);

// Если выбрана специализация — фильтруем юристов
$spec_id = $_GET['spec_id'] ?? '';
if ($spec_id) {
    $stmt = $connection->prepare('SELECT id_worker, full_name FROM worker WHERE id_specialization = ?');
    $stmt->bind_param('i', $spec_id);
    $stmt->execute();
    $workers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $workers = [];
}

// Получаем все услуги
$svcRes = $connection->query('SELECT id_service, service_name, price FROM services');
$services = $svcRes->fetch_all(MYSQLI_ASSOC);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_worker = intval($_POST['id_worker'] ?? 0);
    $chosen = $_POST['services'] ?? [];  // массив id_service

    if (!$id_worker) {
        $errors[] = 'Выберите юриста.';
    }
    if (empty($chosen)) {
        $errors[] = 'Выберите хотя бы одну услугу.';
    }

    if (empty($errors)) {
        // 1) Вставляем заявку
        $stmt = $connection->prepare(
            'INSERT INTO application (data_start, status, id_klient, id_worker)
             VALUES (NOW(), "pending", ?, ?)'
        );
        $stmt->bind_param('ii', $user_id, $id_worker);
        $stmt->execute();
        $app_id = $stmt->insert_id;

        // 2) Вставляем услуги к заявке
        $stmt2 = $connection->prepare(
            'INSERT INTO application_services (id_application, id_services)
             VALUES (?, ?)'
        );
        foreach ($chosen as $svc_id) {
            $svc_id = intval($svc_id);
            $stmt2->bind_param('ii', $app_id, $svc_id);
            $stmt2->execute();
        }

        // Перенаправляем в профиль
        header('Location: /profile.php');
        exit;
    }
}

include __DIR__ . '/templates/header.php';
?>

<h2>Оформить новую заявку</h2>

<?php if (!empty($errors)): ?>
  <ul class="errors">
    <?php foreach ($errors as $err): ?>
      <li><?= e($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="get" action="">
  <label>Фильтр по специализации:</label>
  <select name="spec_id" onchange="this.form.submit()">
    <option value="">— Все специализации —</option>
    <?php foreach ($specializations as $spec): ?>
      <option value="<?= $spec['id_specialization'] ?>"
        <?= $spec_id == $spec['id_specialization'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?>
      </option>
    <?php endforeach; ?>
  </select>
</form>

<form method="post" action="">
  <h3>Выберите юриста</h3>
  <?php if (empty($workers) && $spec_id): ?>
    <p>Нет юристов в данной специализации.</p>
  <?php else: ?>
    <?php foreach ($workers as $w): ?>
      <label>
        <input type="radio" name="id_worker" value="<?= $w['id_worker'] ?>"
          <?= ($_POST['id_worker'] ?? '') == $w['id_worker'] ? 'checked' : '' ?>>
        <?= htmlspecialchars($w['full_name'], ENT_QUOTES, 'UTF-8') ?>
      </label><br>
    <?php endforeach; ?>
  <?php endif; ?>

  <h3>Услуги</h3>
  <?php foreach ($services as $s): ?>
    <label>
      <input type="checkbox" name="services[]" value="<?= $s['id_service'] ?>"
        <?= in_array($s['id_service'], $_POST['services'] ?? []) ? 'checked' : '' ?>>
      <?= htmlspecialchars($s['service_name'], ENT_QUOTES, 'UTF-8') ?>
      (<?= number_format($s['price'], 0, '.', ' ') ?> ₽)
    </label><br>
  <?php endforeach; ?>

  <button type="submit">Отправить заявку</button>
</form>

<?php include __DIR__ . '/templates/footer.php'; ?>
