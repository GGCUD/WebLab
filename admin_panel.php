<?php
session_start();
require_once 'admin_header.php'; 
require_once 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Получение данных клиента и заявок
$client_id = $_GET['client_id'] ?? null;
$client = [];
$applications = [];
$services = $connection->query("SELECT * FROM services")->fetch_all(MYSQLI_ASSOC);
$workers = $connection->query("SELECT * FROM worker")->fetch_all(MYSQLI_ASSOC);

if ($client_id) {
    // Данные клиента
    $stmt = $connection->prepare("SELECT * FROM klient WHERE id_klient = ?");
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();

    // Заявки клиента с услугами и юристом
    $stmt = $connection->prepare("
        SELECT 
            a.id_application, 
            a.data_start, 
            a.data_end, 
            a.status,
            w.full_name AS lawyer,
            GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
        FROM application a
        LEFT JOIN worker w ON a.id_worker = w.id_worker
        LEFT JOIN application_services asv ON a.id_application = asv.id_application
        LEFT JOIN services s ON asv.id_services = s.id_service
        WHERE a.id_klient = ?
        GROUP BY a.id_application
    ");
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $application_id = $_POST['application_id'] ?? null;

    if ($action === 'delete' && $application_id) {
        // Удаление заявки
        $stmt = $connection->prepare("DELETE FROM application WHERE id_application = ?");
        $stmt->bind_param('i', $application_id);
        $stmt->execute();
    } elseif (in_array($action, ['add', 'edit'])) {
        // Добавление/редактирование заявки
        $data_start = $_POST['data_start'];
        $data_end = $_POST['data_end'] ?? null;
        $status = $_POST['status'];
        $id_worker = $_POST['id_worker'];
        $selected_services = $_POST['services'] ?? [];

        if ($action === 'add') {
            $stmt = $connection->prepare("
                INSERT INTO application 
                (id_klient, data_start, data_end, status, id_worker) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('isssi', $client_id, $data_start, $data_end, $status, $id_worker);
            $stmt->execute();
            $application_id = $connection->insert_id;
        } else {
            $stmt = $connection->prepare("
                UPDATE application 
                SET data_start = ?, data_end = ?, status = ?, id_worker = ? 
                WHERE id_application = ?
            ");
            $stmt->bind_param('sssii', $data_start, $data_end, $status, $id_worker, $application_id);
            $stmt->execute();
        }

        // Обновление услуг
        $connection->query("DELETE FROM application_services WHERE id_application = $application_id");
        foreach ($selected_services as $service_id) {
            $stmt = $connection->prepare("
                INSERT INTO application_services (id_application, id_services) 
                VALUES (?, ?)
            ");
            $stmt->bind_param('ii', $application_id, $service_id);
            $stmt->execute();
        }
    }
    header("Location: admin_panel.php?client_id=$client_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
<style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f0f0f0;
        }
        .test-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .error {
            background: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .warning {
            background: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
        }
        form {
            margin: 20px 0;
        }
        input[type="text"] {
            width: 300px;
            padding: 5px;
        }
        .auth {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background:rgb(205, 255, 255);
            border: 2px rgb(74, 142, 151);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgb(74, 142, 151);
        }

        .auth h1 {
            color:rgb(4, 97, 133);
            font-size: 24px;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 2px rgb(74, 142, 151);
            padding-bottom: 0.5rem;
        }

        .auth p {
            font-size: 18px;
            line-height: 1.5;
            margin: 1rem 0;
            padding-left: 1.5rem;
            position: relative;
            color:rgb(4, 118, 133);
        }

    </style>
</head>
<body>

      <div class="auth">
            <h1>Предупреждение!</h1>
            <p style="line-height: 0.2em; margin-left: 10px; font-size: 20px;">Доступны такие действия как:</p>
			<p style="line-height: 0.2em; margin-left: 10px; font-size: 20px;">• Обновловление</p>
			<p style="line-height: 0.2em; margin-left: 10px; font-size: 20px;">• Удаление</p>
			<p style="line-height: 0.2em; margin-left: 10px; font-size: 20px;">• Добавление</p>
			<p style="line-height: 0.2em; margin-left: 10px; font-size: 20px;">Каждое из этих действий будет иметь последствие.</p>
        </div>   
    <!--
    <a href="admin_logout.php">Выйти</a>
    

    <form method="get">
        <input type="text" name="client_id" placeholder="ID клиента" required>
        <button type="submit">Найти</button>
    </form>

    <?php if ($client_id && $client): ?>
        <h2>Клиент: <?= $client['full_name'] ?></h2>
      
        <form method="post">
            <input type="hidden" name="action" value="<?= $action ?>">
            <input type="date" name="data_start" required>
            <input type="date" name="data_end">
            <select name="status">
                <option value="pending">В обработке</option>
                <option value="accepted">Принято</option>
            </select>
            <select name="id_worker">
                <?php foreach ($workers as $worker): ?>
                    <option value="<?= $worker['id_worker'] ?>"><?= $worker['full_name'] ?></option>
                <?php endforeach; ?>
            </select>
            <div>
                <?php foreach ($services as $service): ?>
                    <label>
                        <input type="checkbox" name="services[]" value="<?= $service['id_service'] ?>">
                        <?= $service['service_name'] ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit">Сохранить</button>
        </form>

       
        <table>
            <tr>
                <th>ID</th>
                <th>Дата начала</th>
                <th>Дата окончания</th>
                <th>Статус</th>
                <th>Юрист</th>
                <th>Услуги</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id_application'] ?></td>
                    <td><?= $app['data_start'] ?></td>
                    <td><?= $app['data_end'] ?? '—' ?></td>
                    <td><?= $app['status'] ?></td>
                    <td><?= $app['lawyer'] ?></td>
                    <td><?= $app['services'] ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="application_id" value="<?= $app['id_application'] ?>">
                            <button type="submit">Удалить</button>
                        </form>
                        <a href="?client_id=<?= $client_id ?>&edit=<?= $app['id_application'] ?>">Редактировать</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
            -->
</body>
</html>