<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Установка локали и часового пояса
setlocale(LC_TIME, 'ru_RU.UTF-8');
date_default_timezone_set('Europe/Moscow');

// Обработка массового изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!empty($_POST['selected']) && !empty($_POST['new_status'])) {
        $selected = $_POST['selected'];
        $newStatus = $connection->real_escape_string($_POST['new_status']);
        
        $ids = implode(',', array_map('intval', $selected));
        $query = "UPDATE application SET status = '$newStatus' WHERE id_application IN ($ids)";
        
        if ($connection->query($query)) {
            $success = "Статус успешно обновлен для выбранных заявок";
        } else {
            $error = "Ошибка при обновлении статуса: " . $connection->error;
        }
    } else {
        $error = "Выберите заявки и новый статус";
    }
}

// Получение заявок с деталями
$query = "
    SELECT 
        a.id_application,
        k.full_name AS client_name,
        w.full_name AS worker_name,
        GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services,
        a.data_start,
        a.status
    FROM application a
    LEFT JOIN klient k ON a.id_klient = k.id_klient
    LEFT JOIN worker w ON a.id_worker = w.id_worker
    LEFT JOIN application_services ap ON a.id_application = ap.id_application
    LEFT JOIN services s ON ap.id_services = s.id_service
    GROUP BY a.id_application
";
$applications = $connection->query($query)->fetch_all(MYSQLI_ASSOC);

// Доступные статусы
$statuses = [
    'pending' => 'В обработке',
    'accepted' => 'Завершено',
];
?>

<h1>Управление заявками</h1>

<?php if (isset($success)): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<a href="edit_application.php">Добавить заявку</a>

<form method="post">
<div class="bulk-actions">
    <select name="new_status" required>
        <option value="">Выберите новый статус</option>
        <?php foreach ($statuses as $key => $value): ?>
            <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($value) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="update_status">Применить к выбранным</button>
</div>

<table>
    <tr>
        <th>Выбрать</th>
        <th>ID</th>
        <th>Клиент</th>
        <th>Работник</th>
        <th>Услуги</th>
        <th>Дата и время начала</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($applications as $app): ?>
        <tr>
            <td><input type="checkbox" name="selected[]" value="<?= htmlspecialchars($app['id_application']) ?>"></td>
            <td><?= htmlspecialchars($app['id_application']) ?></td>
            <td><?= htmlspecialchars($app['client_name']) ?></td>
            <td><?= htmlspecialchars($app['worker_name']) ?></td>
            <td><?= htmlspecialchars($app['services']) ?></td>
            <td>
                <?php if ($app['data_start']): ?>
                    <?= htmlspecialchars(
                        date('d.m.Y H:i:s', strtotime($app['data_start']))
                    ) ?>
                <?php else: ?>
                    Не указано
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($statuses[$app['status']] ?? $app['status']) ?></td>
            <td>
                <a href="edit_application.php?id=<?= $app['id_application'] ?>">✏️</a>
                <a href="delete_application.php?id=<?= $app['id_application'] ?>">❌</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</form>

<?php include 'footer.php'; ?>

<style>
.success {
    color: green;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid green;
}

.error {
    color: red;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid red;
}

.bulk-actions {
    margin: 15px 0;
    padding: 10px;
    background: #f5f5f5;
    display: flex;
    gap: 10px;
    align-items: center;
}

.bulk-actions select {
    padding: 5px;
    min-width: 200px;
}

.bulk-actions button {
    padding: 5px 15px;
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

.bulk-actions button:hover {
    background: #45a049;
}
</style>