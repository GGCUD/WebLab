<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$client = [];
$error = '';

// Режим редактирования
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM klient WHERE id_klient = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();
    
    if (!$client) {
        $_SESSION['error'] = 'Клиент не найден';
        header('Location: clients.php');
        exit;
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone_num = trim($_POST['phone_num']);
    $passport = trim($_POST['passport']);
    $password = trim($_POST['password']);
    $mail = trim($_POST['mail']);

    // Валидация
    if (empty($full_name) || empty($phone_num) || empty($passport)) {
        $error = 'Заполните обязательные поля (ФИО, телефон, паспорт)';
    } else {
        // Обновление или добавление
        if ($id > 0) {
            $stmt = $connection->prepare("
                UPDATE klient SET 
                    full_name = ?, 
                    address = ?, 
                    phone_num = ?, 
                    passport = ?, 
                    password = ?, 
                    mail = ? 
                WHERE id_klient = ?
            ");
            $stmt->bind_param('ssssssi', $full_name, $address, $phone_num, $passport, $password, $mail, $id);
        } else {
            $stmt = $connection->prepare("
                INSERT INTO klient 
                    (full_name, address, phone_num, passport, password, mail) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('ssssss', $full_name, $address, $phone_num, $passport, $password, $mail);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = $id > 0 ? 'Данные клиента обновлены' : 'Клиент добавлен';
            header('Location: clients.php');
            exit;
        } else {
            $error = 'Ошибка сохранения: ' . $connection->error;
        }
    }
}
?>

<main class="admin-main">
    <h1><?= $client ? 'Редактирование' : 'Добавление' ?> клиента</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $client['id_klient'] ?? 0 ?>">
        
        <div class="form-group">
            <label>ФИО *</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($client['full_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Адрес</label>
            <input type="text" name="address" value="<?= htmlspecialchars($client['address'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Телефон *</label>
            <input type="tel" name="phone_num" value="<?= htmlspecialchars($client['phone_num'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Паспорт *</label>
            <input type="text" name="passport" value="<?= htmlspecialchars($client['passport'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Пароль *</label>
            <input type="password" name="password" value="<?= htmlspecialchars($client['password'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="mail" value="<?= htmlspecialchars($client['mail'] ?? '') ?>">
        </div>

        <div class="form-actions">
            <button type="submit">Сохранить</button>
            <a href="clients.php" class="cancel-btn">Отмена</a>
        </div>
    </form>
</main>
<style>
    /* Общие стили админки */
    .admin-main {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 20px;
    }

    h1 {
        color: #2c3e50;
        margin-bottom: 2rem;
        font-size: 1.8rem;
        border-bottom: 2px solid #465DCA;
        padding-bottom: 0.5rem;
    }

    /* Стили формы */
    form {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #4a5568;
    }

    .form-group label:after {
        content: " *";
        color: #e53e3e;
        opacity: <?= isset($client) ? '0' : '1' ?>; /* Показывать звездочку только при создании */
    }

    input[type="text"],
    input[type="tel"],
    input[type="password"],
    input[type="email"] {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    input:focus {
        outline: none;
        border-color: #465DCA;
        box-shadow: 0 0 0 2px rgba(70, 93, 202, 0.1);
    }

    /* Стили кнопок */
    button[type="submit"] {
        background: #465DCA;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
        background: #3a4dab;
    }
    .cancel-btn {
        background:rgb(209, 229, 255);
        color:rgb(68, 81, 104) !important;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        text-decoration: none;
        margin-left: 1rem;
        border: 1px solid #cbd5e0;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .cancel-btn:hover {
        background: #edf2f7 !important;
        border-color: #a0aec0;
        color: #2d3748 !important;
    }
    /* Сообщения об ошибках */
    .alert.error {
        background: #fed7d7;
        color: #c53030;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        border: 1px solid #feb2b2;
    }
</style>
<?php include 'footer.php'; ?>