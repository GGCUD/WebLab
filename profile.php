<?php
// profile.php — личный кабинет клиента
require_once 'auth.php';
require_once 'db_connect.php';

$user = $current_user;

// Получаем список заявок (оригинальная логика)
$stmt = $connection->prepare(
    'SELECT a.id_application, a.data_start, a.data_end, a.status,
            GROUP_CONCAT(s.service_name SEPARATOR ", ") AS services,
            w.full_name AS lawyer
     FROM application AS a
     JOIN application_services AS asv ON a.id_application = asv.id_application
     JOIN services AS s ON asv.id_services = s.id_service
     JOIN worker AS w ON a.id_worker = w.id_worker
     WHERE a.id_klient = ?
     GROUP BY a.id_application
     ORDER BY a.data_start DESC'
);
$stmt->bind_param('i', $user['id_klient']);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<main class="profile-wrapper">
    <!-- Шапка профиля -->
    <div class="profile-header">
        <h1 class="welcome-msg"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="order.php" class="new-order-btn">Новая заявка</a>
    </div>

    <!-- Блок с данными пользователя -->
    <section class="profile-section">
        <h2 class="section-title">Личная информация</h2>
        <div class="user-details-grid">
            <div class="detail-item">
                <span class="detail-label">Телефон:</span>
                <span class="detail-value"><?= htmlspecialchars($user['phone_num'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Адрес:</span>
                <span class="detail-value"><?= htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Паспорт:</span>
                <span class="detail-value"><?= htmlspecialchars($user['passport'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?= htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    </section>

    <!-- Блок с заявками -->
    <section class="profile-section">
        <h2 class="section-title">Ваши заявки</h2>
        
        <?php if ($result->num_rows === 0): ?>
            <div class="no-orders">
                <p>Нет активных заявок</p>
                <a href="order.php" class="new-order-btn">Создать заявку</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">Заявка #<?= $row['id_application'] ?></span>
                            <span class="status-badge <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
                        </div>
                        <div class="order-details">
                            <div class="detail">
                                <span class="detail-label">Юрист:</span>
                                <?= htmlspecialchars($row['lawyer'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Услуги:</span>
                                <?= htmlspecialchars($row['services'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="detail">
                                <span class="detail-label">Дата подачи:</span>
                                <?= $row['data_start'] ?>
                            </div>
                            <?php if ($row['data_end']): ?>
                            <div class="detail">
                                <span class="detail-label">Дата завершения:</span>
                                <?= $row['data_end'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<style>
    /* Базовые стили */
    .profile-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        margin-top: 70px; /* Высота хедера */
        padding: 40px 20px 20px; /* Верхний отступ */
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        padding-top: 30px; /* Дополнительный отступ внутри блока */
        margin-top: 0;
    }

    .welcome-msg {
        font-size: 2rem;
        color: #2d3748;
        margin: 0;
    }

    /* Секции */
    .profile-section {
        background: #ffffff;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .section-title {
        font-size: 1.5rem;
        color: #4a5568;
        margin: 0 0 1.5rem 0;
    }

    /* Данные пользователя */
    .user-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.2rem;
    }

    .detail-item {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 6px;
    }

    .detail-label {
        display: block;
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .detail-value {
        color: #2d3748;
        font-weight: 500;
    }

    /* Список заявок */
    .orders-list {
        display: grid;
        gap: 1.2rem;
    }

    .order-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        transition: transform 0.2s;
    }

    .order-card:hover {
        transform: translateY(-2px);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .order-id {
        font-weight: 600;
        color: #2d3748;
    }

    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .status-badge.pending { background: #f6e05e33; color: #b7791f; }
    .status-badge.completed { background: #68d39133; color: #22543d; }
    .status-badge.canceled { background: #fc818133; color: #742a2a; }

    .order-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    /* Кнопки */
    .new-order-btn {
        background: #4299e1;
        color: white;
        padding: 0.7rem 1.4rem;
        border-radius: 6px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .new-order-btn:hover {
        background: #3182ce;
    }

    .no-orders {
        text-align: center;
        padding: 2rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .order-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include 'footer.php'; ?>