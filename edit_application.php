<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$application = [];
$clients = $connection->query("SELECT * FROM klient")->fetch_all(MYSQLI_ASSOC);
$workers = $connection->query("SELECT * FROM worker")->fetch_all(MYSQLI_ASSOC);
$services = $connection->query("SELECT * FROM services")->fetch_all(MYSQLI_ASSOC);
$selected_services = [];

// Режим редактирования
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM application WHERE id_application = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $application = $stmt->get_result()->fetch_assoc();

    // Получение выбранных услуг
    $stmt = $connection->prepare("SELECT id_services FROM application_services WHERE id_application = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $selected_services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $selected_services = array_column($selected_services, 'id_services');
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $id_klient = (int)$_POST['id_klient'];
    $id_worker = (int)$_POST['id_worker'];
    $data_start = $_POST['data_start'];
    $status = $_POST['status'];
    $services = $_POST['services'] ?? [];

    if ($id > 0) {
        // Обновление заявки
        $stmt = $connection->prepare("
            UPDATE application SET 
                id_klient = ?, 
                id_worker = ?, 
                data_start = ?, 
                status = ? 
            WHERE id_application = ?
        ");
        $stmt->bind_param('iissi', $id_klient, $id_worker, $data_start, $status, $id);
    } else {
        // Добавление заявки
        $stmt = $connection->prepare("
            INSERT INTO application 
                (id_klient, id_worker, data_start, status) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('iiss', $id_klient, $id_worker, $data_start, $status);
    }

    if ($stmt->execute()) {
        $application_id = $id > 0 ? $id : $connection->insert_id;

        // Обновление услуг
        $connection->query("DELETE FROM application_services WHERE id_application = $application_id");
        foreach ($services as $service_id) {
            $stmt = $connection->prepare("
                INSERT INTO application_services 
                    (id_application, id_services) 
                VALUES (?, ?)
            ");
            $stmt->bind_param('ii', $application_id, $service_id);
            $stmt->execute();
        }

        $_SESSION['success'] = $id > 0 ? 'Заявка обновлена' : 'Заявка добавлена';
        header('Location: applications.php');
        exit;
    } else {
        $error = 'Ошибка сохранения: ' . $connection->error;
    }
}
?>
<main class="admin-main">
    <h1><?= $application ? 'Редактирование' : 'Добавление' ?> заявки</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="application-form">
        <input type="hidden" name="id" value="<?= $application['id_application'] ?? 0 ?>">
        
        <div class="form-group">
            <label>Клиент</label>
            <select name="id_klient" required>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id_klient'] ?>" 
                        <?= ($client['id_klient'] == ($application['id_klient'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($client['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Работник</label>
            <select name="id_worker" required>
                <?php foreach ($workers as $worker): ?>
                    <option value="<?= $worker['id_worker'] ?>" 
                        <?= ($worker['id_worker'] == ($application['id_worker'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($worker['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Дата и время начала</label>
            <input type="datetime-local" name="data_start" 
                value="<?= htmlspecialchars($application['data_start'] ?? '') ?>" 
                required>
        </div>

        <div class="form-group">
            <label>Статус заявки</label>
            <select name="status" required>
                <option value="pending" <?= ($application['status'] ?? '') === 'pending' ? 'selected' : '' ?>>В обработке</option>
                <option value="accepted" <?= ($application['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>Принято</option>
            </select>
        </div>

        <div class="form-group">
            <label>Выберите услуги</label>
            <div class="services-group">
                <?php foreach ($services as $service): ?>
                    <label class="service-checkbox">
                        <input type="checkbox" 
                               name="services[]" 
                               value="<?= $service['id_service'] ?>" 
                               <?= in_array($service['id_service'], $selected_services) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($service['service_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn"> Сохранить изменения</button>
            <a href="applications.php" class="cancel-btn"> Отменить</a>
        </div>
    </form>
<style>
    /* Основные стили */
    .admin-main {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1 {
        color: #2c3e50;
        font-size: 2rem;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #465DCA;
    }

    /* Стили формы */
    .application-form {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
        font-size: 0.95rem;
    }

    select, 
    input[type="datetime-local"] {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    select:focus, 
    input[type="datetime-local"]:focus {
        border-color: #465DCA;
        box-shadow: 0 0 0 3px rgba(70, 93, 202, 0.1);
        outline: none;
        background: white;
    }

    /* Стили для чекбоксов */
    .services-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }

    .service-checkbox {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #f8fafc;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .service-checkbox:hover {
        border-color: #465DCA;
        background: white;
    }

    .service-checkbox input {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        accent-color: #465DCA;
    }

    /* Кнопки */
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 35px;
        padding-top: 25px;
        border-top: 2px solid #edf2f7;
    }

    .save-btn {
        background: #465DCA;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .save-btn:hover {
        background: #3a4dab;
    }

    .cancel-btn {
        background: #f8fafc;
        color: #4a5568 !important;
        padding: 12px 25px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .cancel-btn:hover {
        border-color: #465DCA;
        color: #465DCA !important;
        background: white;
    }

    /* Сообщения об ошибках */
    .alert.error {
        background: #fee2e2;
        color: #dc2626;
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 6px;
        border: 2px solid #fecaca;
        font-size: 0.95rem;
    }
</style>
