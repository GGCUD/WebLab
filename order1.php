<?php
// order.php — оформление заявки из корзины
require_once 'auth.php';      // старт сессии, проверка авторизации, $current_user
require_once 'db_connect.php';

$user_id = $current_user['id_klient'];
$errors = [];

// Если корзина пуста — перенаправляем на страницу корзины
if (empty($_SESSION['cart'])) {
    header('Location: /cart.php');
    exit;
}

// Получаем доступ к услугам из корзины
$service_ids = $_SESSION['cart'];

// Подготовим IN‑список для получения данных об услугах
$placeholders = implode(',', array_fill(0, count($service_ids), '?'));
$types = str_repeat('i', count($service_ids));
$stmt = $connection->prepare(
    "SELECT id_service, service_name, price 
     FROM services 
     WHERE id_service IN ($placeholders)"
);
$stmt->bind_param($types, ...$service_ids);
$stmt->execute();
$svcResult = $stmt->get_result();

// Собираем телефонов услуг для отображения и подсчёта общей стоимости
$services = [];
$total = 0;
while ($row = $svcResult->fetch_assoc()) {
    $services[] = $row;
    $total += (int)$row['price'];
}

// Если форма отправлена (подтверждение заявки)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    // Вы можете дать пользователю выбрать юриста заранее, 
    // но здесь для простоты возьмём первого подходящего
    // или можно заранее сохранить выбранного юриста в сессии.
    // Давайте запросим всех юристов и выберем первого:
    $wRes = $connection->query('SELECT id_worker FROM worker LIMIT 1');
    $worker = $wRes->fetch_assoc();
    $id_worker = $worker['id_worker'];

    // 1) Сохраняем заявку
    $stmtIns = $connection->prepare(
        'INSERT INTO application (data_start, status, id_klient, id_worker)
         VALUES (NOW(), "pending", ?, ?)'
    );
    $stmtIns->bind_param('ii', $user_id, $id_worker);
    $stmtIns->execute();
    $app_id = $stmtIns->insert_id;

    // 2) Связываем услуги с заявкой
    $stmtSvc = $connection->prepare(
        'INSERT INTO application_services (id_application, id_services)
         VALUES (?, ?)'
    );
    foreach ($service_ids as $svc_id) {
        $svc_id = (int)$svc_id;
        $stmtSvc->bind_param('ii', $app_id, $svc_id);
        $stmtSvc->execute();
    }

    // 3) Очищаем корзину
    $_SESSION['cart'] = [];

    // 4) Редирект в профиль
    header('Location: /profile.php');
    exit;
}

include __DIR__ . 'header.php';
?>

<h2>Оформление заявки</h2>

<p>Выбрано услуг: <strong><?= count($services) ?></strong>, общая стоимость: <strong><?= number_format($total, 0, '.', ' ') ?> ₽</strong></p>

<table class="data-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Услуга</th>
      <th>Цена</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($services as $s): ?>
      <tr>
        <td><?= $s['id_service'] ?></td>
        <td><?= htmlspecialchars($s['service_name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= number_format($s['price'], 0, '.', ' ') ?> ₽</td>
      </tr>
    <?php endforeach; ?>
      <tr>
        <td colspan="2"><strong>Итого</strong></td>
        <td><strong><?= number_format($total, 0, '.', ' ') ?> ₽</strong></td>
      </tr>
  </tbody>
</table>

<form method="post">
  <button type="submit" name="confirm">Подтвердить и отправить заявку</button>
  <a href="cart.php">Вернуться в корзину</a>
</form>

<?php include __DIR__ . '/templates/footer.php'; ?>
