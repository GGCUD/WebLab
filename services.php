<?php
// services.php — просмотр и поиск услуг
require_once 'session.php';
require_once 'db_connect.php';

// Получаем строку поиска из GET
$search = trim($_GET['search'] ?? '');

// Готовим запрос
if ($search !== '') {
    $stmt = $connection->prepare(
        'SELECT id_service, service_name, price 
         FROM services 
         WHERE service_name LIKE ?'
    );
    $like = '%' . $search . '%';
    $stmt->bind_param('s', $like);
} else {
    $stmt = $connection->prepare(
        'SELECT id_service, service_name, price 
         FROM services'
    );
}

$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<h2>Услуги</h2>

<form method="get" action="">
    <input type="text" name="search" placeholder="Поиск по названию" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Искать</button>
    <?php if ($search !== ''): ?>
        <a href="services.php">Сбросить</a>
    <?php endif; ?>
</form>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название услуги</th>
            <th>Цена (₽)</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="3">По запросу ничего не найдено</td></tr>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_service'] ?></td>
                <td><?= htmlspecialchars($row['service_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format($row['price'], 0, '.', ' ') ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
