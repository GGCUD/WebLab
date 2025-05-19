<?php
// cart.php — страница корзины услуг
require_once 'session.php';
require_once 'db_connect.php';

// Инициализируем корзину в сессии
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка действий: add, remove, clear
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'add' && $id > 0) {
    // Добавляем в корзину (предотвращаем дубли)
    if (!in_array($id, $_SESSION['cart'], true)) {
        $_SESSION['cart'][] = $id;
    }
    header('Location: cart.php');
    exit;
}

if ($action === 'remove' && $id > 0) {
    // Удаляем из корзины
    $_SESSION['cart'] = array_filter(
        $_SESSION['cart'],
        fn($svc) => $svc !== $id
    );
    header('Location: cart.php');
    exit;
}

if ($action === 'clear') {
    // Очищаем корзину
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

// Получаем данные по выбранным услугам
$services = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    // Соберём плейсхолдеры для IN‑запроса
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $types = str_repeat('i', count($_SESSION['cart']));
    $stmt = $connection->prepare(
        "SELECT id_service, service_name, price 
         FROM services 
         WHERE id_service IN ($placeholders)"
    );
    $stmt->bind_param($types, ...$_SESSION['cart']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $services[] = $row;
        $total += (int)$row['price'];
    }
}
?>

<?php include 'header.php'; ?>

<h2>Ваша корзина</h2>

<?php if (empty($services)): ?>
  <p>Корзина пуста. <a href="services.php">Перейти к услугам</a></p>
<?php else: ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Услуга</th>
        <th>Цена (₽)</th>
        <th>Действие</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($services as $svc): ?>
        <tr>
          <td><?= $svc['id_service'] ?></td>
          <td><?= htmlspecialchars($svc['service_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($svc['price'], 0, '.', ' ') ?></td>
          <td>
            <a href="?action=remove&id=<?= $svc['id_service'] ?>">Удалить</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="2"><strong>Итого</strong></td>
        <td colspan="2"><strong><?= number_format($total, 0, '.', ' ') ?> ₽</strong></td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top:1em;">
    <a href="?action=clear">Очистить корзину</a> |
    <a href="order.php">Оформить заявку</a>
  </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
