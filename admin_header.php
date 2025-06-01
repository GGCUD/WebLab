
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Административная панель</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-nav {
            background: rgb(66, 76, 161);
            padding: 1rem;
        }
        .admin-nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .admin-nav a:hover {
            color: #3498db;
        }
        .content {
            /* Растягиваем эту зону, чтобы «оттолкнуть» футер вниз */
            flex: 1;
            padding: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>
    <nav class="admin-nav">
            <ul>
            <li><a href="clients.php">Клиенты</a></li>
            <li><a href="workers.php">Работники</a></li>
            <li><a href="applications.php">Заявки</a></li>
            <li><a href="admin_services.php">Услуги</a></li>
            <li><a href="admin_news.php">Новости</a></li>
            <li style="margin-left: auto;"><a href="admin_logout.php">Выйти</a></li>
        </ul>  <!-- навигация -->
    </nav>
    <!--<div class="content">-->
    <main>    
