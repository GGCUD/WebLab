<?php
require_once 'db_connect.php';

$sql = "SELECT id_service, service_name, price FROM services";
$result = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<?php include 'header.php'; ?>

<head>
    <meta charset="UTF-8">
    <title>Список услуг компании</title>
    <style>
        /* Общие стили */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            background: #f8fafc;
        }

        /* Основной контейнер */
        .auth-section {
            padding: 80px 0;
        }

        .auth-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .section-title {
            color: #2b6cb0;
            font-size: 1.75rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Стили таблицы */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .services-table th {
            background: #2b6cb0;
            color: white;
            padding: 0.75rem;
            text-align: left;
            font-weight: 500;
        }

        .services-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
        }

        .services-table tr:last-child td {
            border-bottom: none;
        }

        .services-table tr:hover td {
            background-color: #ebf8ff;
        }

        /* Стили для цены */
        .price {
            font-weight: 600;
            color: #2b6cb0;
        }

        /* Дополнительные элементы */
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <section class="auth-section">
        <div class="auth-container">
            <h1 class="section-title">Наши услуги</h1>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Название услуги</th>
                            <th>Цена (руб.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                            <td class="price"><?= number_format($row['price'], 0, ', ', ' ') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert">
                    <?= $result ? 'Услуги не найдены' : 
                    'Ошибка выполнения запроса: '.$connection->error ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
<?php include 'footer.php'; ?>

</html>
