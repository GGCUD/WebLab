<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$worker = [];
$specializations = $connection->query("SELECT * FROM specialization")->fetch_all(MYSQLI_ASSOC);

// Режим редактирования
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM worker WHERE id_worker = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $worker = $stmt->get_result()->fetch_assoc();
    
    if (!$worker) {
        $_SESSION['error'] = 'Работник не найден';
        header('Location: workers.php');
        exit;
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $full_name = trim($_POST['full_name']);
    $birh_date = $_POST['birh_date'];
    $phone_num = trim($_POST['phone_num']);
    $id_specialization = (int)$_POST['id_specialization'];

    // Валидация
    if (empty($full_name) || empty($birh_date) || empty($phone_num)) {
        $_SESSION['error'] = 'Заполните все обязательные поля';
        header("Location: edit_worker.php?id=$id");
        exit;
    }

    // Обновление или создание
    if ($id > 0) {
        $stmt = $connection->prepare("
            UPDATE worker SET 
                full_name = ?, 
                birh_date = ?, 
                phone_num = ?, 
                id_specialization = ? 
            WHERE id_worker = ?
        ");
        $stmt->bind_param('sssii', $full_name, $birh_date, $phone_num, $id_specialization, $id);
    } else {
        $stmt = $connection->prepare("
            INSERT INTO worker 
                (full_name, birh_date, phone_num, id_specialization) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('sssi', $full_name, $birh_date, $phone_num, $id_specialization);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id > 0 ? 'Данные обновлены' : 'Работник добавлен';
        header('Location: workers.php');
        exit;
    } else {
        $_SESSION['error'] = 'Ошибка сохранения';
        header("Location: edit_worker.php?id=$id");
        exit;
    }
}
?>

<main class="admin-main">
    <h1><?= isset($worker['id_worker']) ? 'Редактирование' : 'Добавление' ?> работника</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']) ?>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $worker['id_worker'] ?? 0 ?>">
        
        <div class="form-group">
            <label>ФИО:</label>
            <input type="text" name="full_name" 
                value="<?= htmlspecialchars($worker['full_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Дата рождения:</label>
            <input type="date" name="birh_date" 
                value="<?= htmlspecialchars($worker['birh_date'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Телефон:</label>
            <input type="tel" name="phone_num" 
                value="<?= htmlspecialchars($worker['phone_num'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Специализация:</label>
            <select name="id_specialization" required>
                <?php foreach ($specializations as $spec): ?>
                    <option value="<?= $spec['id_specialization'] ?>"
                        <?= ($spec['id_specialization'] == ($worker['id_specialization'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($spec['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Сохранить</button>
            <a href="workers.php" class="cancel-btn">Отмена</a>
        </div>
    </form>
</main>
<style>
    /* Добавьте эти стили в раздел head или в основной CSS файл */
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
    }

    input[type="text"],
    input[type="date"],
    input[type="tel"],
    select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fff;
    }

    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #465DCA;
        box-shadow: 0 0 0 2px rgba(70, 93, 202, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

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
        background: #f7fafc;
        color: #4a5568;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        text-decoration: none;
        border: 1px solid #cbd5e0;
        transition: all 0.3s ease;
    }

    .cancel-btn:hover {
        background: #edf2f7;
        border-color: #a0aec0;
    }

    .alert.error {
        background: #fed7d7;
        color: #c53030;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        border: 1px solid #feb2b2;
    }

    @media (max-width: 768px) {
        .admin-main {
            padding: 0 10px;
        }
        
        form {
            padding: 1rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        button[type="submit"], 
        .cancel-btn {
            width: 100%;
            margin: 0;
        }
    }
</style>
<?php include 'footer.php'; ?>