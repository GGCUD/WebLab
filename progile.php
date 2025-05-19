<?php
// profile.php — личный кабинет клиента
require_once __DIR__ . '/includes/auth.php';      // старт сессии, проверка авторизации, получает $current_user
require_once __DIR__ . '/includes/db_connect.php';

$user = $current_user;  // данные из auth.php

// Получаем список заявок клиента
$stmt = $connection->prepare(
    'SELECT a.id_application, a.data_start, a.data_end, a.status,
            GROUP_CONCAT(s.service_name SEPARATOR ", ") AS services,
            w.full_name AS lawyer
     FROM application AS a
     JOIN application_services AS asv ON a.id_application = asv.id_application
     JOIN services AS s ON asv.id_services = s.id_service
     JOIN worker AS w ON a.id_worker = w.id_worker
     WHERE a.id_klient = ?
     GROUP BY a.id_application
     ORDER BY a.data_start DESC'
);
$stmt->bind_param('i', $user['id_klient']);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include __DIR__ . '/templates/header.php'; ?>

<h2>Добро пожаловать, <?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>!</h2>

<section>
  <h3>Ваши данные</h3>
  <ul>
    <li><strong>ФИО:</strong> <?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Адрес:</strong> <?= htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Телефон:</strong> <?= htmlspecialchars($user['phone_num'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Паспорт:</strong> <?= htmlspecialchars($user['passport'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8') ?></li>
  </ul>
</section>

<section>
  <h3>Ваши заявки</h3>
  <?php if ($result->num_rows === 0): ?>
    <p>У вас ещё нет заявок. <a href="/order.php">Оформить новую</a></p>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Дата подачи</th>
          <th>Дата решения</th>
          <th>Юрист</th>
          <th>Услуги</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_application'] ?></td>
          <td><?= $row['data_start'] ?></td>
          <td><?= $row['data_end'] ?? '—' ?></td>
          <td><?= htmlspecialchars($row['lawyer'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($row['services'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= ucfirst($row['status']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
