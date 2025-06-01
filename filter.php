<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<?php include 'header.php'; ?>
<head>
    <title>Услуги</title>
</head>
<body>
    
    <h2>Список услуг</h2>
    <form method="GET">
        Сортировать по: 
        <select name="sort">
            <option value="price_asc">Цене (по возрастанию)</option>
            <option value="price_desc">Цене (по убыванию)</option>
        </select>
        <input type="submit" value="Применить">
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Цена</th>
        </tr>
        <?php
        $sort = $_GET['sort'] ?? 'price_asc';
        $order = ($sort == 'price_desc') ? 'DESC' : 'ASC';
        $stmt = $pdo->query("SELECT * FROM services ORDER BY price $order");
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['id_service']}</td>
                    <td>{$row['service_name']}</td>
                    <td>{$row['price']} руб.</td>
                  </tr>";
        }
        ?>
    </table>
</body>
<?php include 'footer.php'; ?>

</html>