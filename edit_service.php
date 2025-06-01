<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

$service = ['id_service' => 0, 'service_name' => '', 'price' => ''];

// Режим редактирования
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM services WHERE id_service = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id_service'];
    $name = trim($_POST['service_name']);
    $price = (int)str_replace(' ', '', $_POST['price']);

    // Валидация
    if (empty($name) || $price <= 0) {
        $_SESSION['error'] = 'Заполните все поля корректно!';
    } else {
        if ($id > 0) {
            // Обновление
            $stmt = $connection->prepare("
                UPDATE services SET 
                    service_name = ?, 
                    price = ? 
                WHERE id_service = ?
            ");
            $stmt->bind_param('sii', $name, $price, $id);
        } else {
            // Добавление
            $stmt = $connection->prepare("
                INSERT INTO services 
                    (service_name, price) 
                VALUES (?, ?)
            ");
            $stmt->bind_param('si', $name, $price);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Изменения сохранены!';
            header('Location: admin_services.php');
            exit;
        } else {
            $_SESSION['error'] = 'Ошибка сохранения: ' . $connection->error;
        }
    }
}
?>

<div class="admin-content">
    <h1><?= $service['id_service'] ? 'Редактирование' : 'Добавление' ?> услуги</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']) ?>
    <?php endif; ?>

    <form method="post" class="service-form">
        <input type="hidden" name="id_service" value="<?= $service['id_service'] ?>">
        
        <div class="form-group">
            <label>Название услуги:</label>
            <input type="text" 
                   name="service_name" 
                   value="<?= htmlspecialchars($service['service_name']) ?>" 
                   required>
        </div>

        <div class="form-group">
            <label>Цена (в рублях):</label>
            <input type="text" 
                   name="price" 
                   value="<?= $service['price'] ? number_format($service['price'], 0, '', ' ') : '' ?>" 
                   required
                   oninput="formatPrice(this)">
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Сохранить</button>
            <a href="admin_services.php" class="cancel-btn">Отмена</a>
        </div>
    </form>
</div>

<script>
    // Форматирование цены при вводе
    function formatPrice(input) {
        let value = input.value.replace(/\s/g, '');
        if (!isNaN(value)) {
            input.value = parseInt(value).toLocaleString('ru-RU');
        }
    }
</script>

<style>
    .service-form {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
</style>

<?php include 'footer.php'; ?>