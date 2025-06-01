<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Обработка параметра фильтрации
$search = $_GET['search'] ?? '';
$where = '';
if (!empty($search)) {
    $search = $connection->real_escape_string($search);
    $where = " WHERE full_name LIKE '%$search%'";
}

// Получение списка клиентов с фильтрацией
$query = "SELECT * FROM klient" . $where;
$clients = $connection->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<h1>Управление клиентами</h1>

<!-- Форма поиска -->
<form method="GET" action="" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Поиск по ФИО" 
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Найти</button>
    <a href="?" style="margin-left: 10px;">Сбросить</a>
</form>

<a href="edit_client.php">Добавить клиента</a>

<table>
    <tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>Паспорт</th>
        <th>Email</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($clients as $client): 
        $phone = preg_replace('/[^0-9]/', '', $client['phone_num']);
        if(strlen($phone) === 11 && in_array($phone[0], ['7', '8'])) {
            $phone = substr($phone, 1);
        }
        $formattedPhone = (strlen($phone) === 10)
            ? '+7 ('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6, 2).'-'.substr($phone, 8, 2)
            : $client['phone_num'];
    ?>
        <tr>
            <td><?= htmlspecialchars($client['id_klient']) ?></td>
            <td><?= htmlspecialchars($client['full_name']) ?></td>
            <td><?= htmlspecialchars($formattedPhone) ?></td>
            <td><?= htmlspecialchars($client['address'] ?? 'Не указан') ?></td>
            <td><?= htmlspecialchars($client['passport']) ?></td>
            <td><?= htmlspecialchars($client['mail']) ?></td>
            <td>
                <a href="edit_client.php?id=<?= $client['id_klient'] ?>">✏️</a>
                <a href="delete_client.php?id=<?= $client['id_klient'] ?>">❌</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>