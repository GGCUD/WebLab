<?php
require_once 'admin_header.php';
require_once 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Удаление связанных услуг
    $stmt = $connection->prepare("DELETE FROM application_services WHERE id_application = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Удаление заявки
    $stmt = $connection->prepare("DELETE FROM application WHERE id_application = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Заявка удалена';
    } else {
        $_SESSION['error'] = 'Ошибка удаления: ' . $connection->error;
    }
}

header('Location: applications.php');
exit;