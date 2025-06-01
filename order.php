<?php
// order.php — оформление заявки
require_once 'auth.php';      // старт сессии, проверка авторизации, $current_user
require_once 'db_connect.php';

$user_id = $current_user['id_klient'];
$errors = [];

// Получаем все специализации для фильтра (опционально)
$specRes = $connection->query('SELECT id_specialization, name FROM specialization');
$specializations = $specRes->fetch_all(MYSQLI_ASSOC);

// Если выбрана специализация — фильтруем юристов
$spec_id = $_GET['spec_id'] ?? '';
if ($spec_id) {
    $stmt = $connection->prepare('SELECT id_worker, full_name FROM worker WHERE id_specialization = ?');
    $stmt->bind_param('i', $spec_id);
    $stmt->execute();
    $workers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $workers = [];
}

// Получаем все услуги
$svcRes = $connection->query('SELECT id_service, service_name, price FROM services');
$services = $svcRes->fetch_all(MYSQLI_ASSOC);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_worker = intval($_POST['id_worker'] ?? 0);
    $chosen = $_POST['services'] ?? [];  // массив id_service

    if (!$id_worker) {
        $errors[] = 'Выберите юриста.';
    }
    if (empty($chosen)) {
        $errors[] = 'Выберите хотя бы одну услугу.';
    }

    if (empty($errors)) {
        // 1) Вставляем заявку
        $stmt = $connection->prepare(
            'INSERT INTO application (data_start, status, id_klient, id_worker)
             VALUES (NOW(), "pending", ?, ?)'
        );
        $stmt->bind_param('ii', $user_id, $id_worker);
        $stmt->execute();
        $app_id = $stmt->insert_id;

        // 2) Вставляем услуги к заявке
        $stmt2 = $connection->prepare(
            'INSERT INTO application_services (id_application, id_services)
             VALUES (?, ?)'
        );
        foreach ($chosen as $svc_id) {
            $svc_id = intval($svc_id);
            $stmt2->bind_param('ii', $app_id, $svc_id);
            $stmt2->execute();
        }

        // Перенаправляем в профиль
        header('Location: /profile.php');
        exit;
    }
}

include 'header.php';
?>

<main class="order-container">
    <h1 class="page-title">Новая заявка</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $err): ?>
                <div class="error-item"><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Фильтр специализаций -->
    <div class="filter-section">
        <form method="get" class="specialization-filter">
            <label>Специализация юриста:</label>
            <select name="spec_id" onchange="this.form.submit()" class="styled-select">
                <option value="">Все специализации</option>
                <?php foreach ($specializations as $spec): ?>
                    <option value="<?= $spec['id_specialization'] ?>"
                        <?= $spec_id == $spec['id_specialization'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($spec['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <form method="post" class="order-form">
        <!-- Блок выбора юриста -->
        <section class="form-section">
            <h2 class="section-title">Выберите юриста</h2>
            
            <?php if (empty($workers) && $spec_id): ?>
                <div class="empty-state">Нет доступных юристов</div>
            <?php else: ?>
                <div class="workers-grid">
                    <?php foreach ($workers as $w): ?>
                        <label class="worker-card">
                            <input type="radio" name="id_worker" value="<?= $w['id_worker'] ?>" 
                                <?= ($_POST['id_worker'] ?? '') == $w['id_worker'] ? 'checked' : '' ?>>
                            <div class="card-content">
                                <h3><?= htmlspecialchars($w['full_name']) ?></h3>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Блок выбора услуг -->
        <section class="form-section">
            <h2 class="section-title">Выберите услуги</h2>
            
            <div class="services-grid">
                <?php foreach ($services as $s): ?>
                    <label class="service-card">
                        <input type="checkbox" name="services[]" value="<?= $s['id_service'] ?>"
                            <?= in_array($s['id_service'], $_POST['services'] ?? []) ? 'checked' : '' ?>>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($s['service_name']) ?></h3>
                            <div class="price"><?= number_format($s['price'], 0, '.', ' ') ?> ₽</div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="submit-btn">Отправить заявку</button>
        </div>
    </form>
</main>

<style>
    /* Основные стили */
    .order-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-title {
        font-size: 2rem;
        color: #2d3748;
        margin-bottom: 2rem;
    }

    /* Фильтр */
    .specialization-filter {
        margin-bottom: 2rem;
    }

    .styled-select {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        font-size: 1rem;
    }

    /* Карточки */
    .workers-grid,
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .worker-card,
    .service-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        position: relative;
    }

    .worker-card input[type="radio"],
    .service-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
    }

    .worker-card:hover,
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .worker-card input:checked + .card-content,
    .service-card input:checked + .card-content {
        border-color: #4299e1;
        background: #ebf8ff;
    }

    .card-content {
        padding: 1rem;
        border: 2px solid transparent;
        border-radius: 6px;
        transition: all 0.2s;
    }

    /* Стили для услуг */
    .price {
        color: #38a169;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    /* Кнопка отправки */
    .submit-btn {
        margin-top: 15px;
        background: #4299e1;
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 6px;
        font-size: 1.1rem;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: #3182ce;
    }

    /* Сообщения об ошибках */
    .alert-error {
        background: #fff5f5;
        color: #c53030;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 2rem;
        border: 1px solid #fed7d7;
    }

    @media (max-width: 768px) {
        .workers-grid,
        .services-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include 'footer.php'; ?>