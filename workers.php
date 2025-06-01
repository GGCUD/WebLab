<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Получение работников с специализациями
$query = "
    SELECT w.*, s.name AS specialization 
    FROM worker w 
    LEFT JOIN specialization s ON w.id_specialization = s.id_specialization
";
$workers = $connection->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<h1>Управление работниками</h1>
<a href="edit_worker.php">Добавить работника</a>
<table>
    <tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Дата рождения</th>
        <th>Телефон</th>
        <th>Специализация</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($workers as $worker): 
        $birthDate = $worker['birth_date'] ?? $worker['birh_date'];
        $phone = preg_replace('/[^0-9]/', '', $worker['phone_num']);
        if(strlen($phone) === 11 && in_array($phone[0], ['7', '8'])) {
            $phone = substr($phone, 1);
        }
        $formattedPhone = (strlen($phone) === 10)
            ? '+7 ('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6, 2).'-'.substr($phone, 8, 2)
            : $worker['phone_num'];
    ?>
        <tr>
            <td><?= htmlspecialchars($worker['id_worker']) ?></td>
            <td><?= htmlspecialchars($worker['full_name']) ?></td>
            <td><?= htmlspecialchars(date('d.m.Y', strtotime($birthDate))) ?></td>
            <td><?= htmlspecialchars($formattedPhone) ?></td>
            <td><?= htmlspecialchars($worker['specialization']) ?></td>
            <td>
                <a href="edit_worker.php?id=<?= $worker['id_worker'] ?>">✏️</a>
                <a href="delete_worker.php?id=<?= $worker['id_worker'] ?>">❌</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>