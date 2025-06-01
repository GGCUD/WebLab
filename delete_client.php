<?php
session_start();
require_once 'db_connect.php';
require_once 'admin_header.php';

// Включение отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка авторизации
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Удаление связанных заявок
        $stmt = $connection->prepare("DELETE FROM application WHERE id_klient = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Удаление клиента
        $stmt = $connection->prepare("DELETE FROM klient WHERE id_klient = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Клиент успешно удален';
        } else {
            throw new Exception('Ошибка удаления: ' . $stmt->error);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log($e->getMessage()); // Запись в лог
    }
}

header('Location: clients.php');
exit();