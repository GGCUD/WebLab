<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Обработка удаления
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM services WHERE id_service = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

// Получение списка услуг
$services = $connection->query("SELECT * FROM services")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-content">
    <h1>Управление услугами</h1>
    <a href="edit_service.php" class="add-button">+ Добавить услугу</a>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= $service['id_service'] ?></td>
                    <td><?= htmlspecialchars($service['service_name']) ?></td>
                    <td><?= number_format($service['price'], 0, '', ' ') ?> ₽</td>
                    <td class="actions">
                        <a href="edit_service.php?id=<?= $service['id_service'] ?>" class="edit-btn">✏️</a>
                        <a href="?delete=<?= $service['id_service'] ?>" class="delete-btn" 
                           onclick="return confirm('Удалить эту услугу?')">❌</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .data-table th, 
    .data-table td {
        padding: 12px;
        border: 1px solid #ddd;
    }
    
    .data-table th {
        background: #f8f8f8;
    }
    
    .actions {
        text-align: center;
    }
    
    .edit-btn, 
    .delete-btn {
        padding: 5px 10px;
        margin: 0 3px;
    }
</style>

<?php include 'footer.php'; ?>