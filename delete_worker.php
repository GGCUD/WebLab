<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $connection->prepare("DELETE FROM worker WHERE id_worker = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Работник удален';
    } else {
        $_SESSION['error'] = 'Ошибка удаления: ' . $connection->error;
    }
}

header('Location: workers.php');
exit;