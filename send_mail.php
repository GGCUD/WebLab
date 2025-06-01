<?php
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$smtpHost = 'smtp.gmail.com';     
$smtpUsername = 'seregariabchenok@gmail.com'; 
$smtpPassword = 'iddc krhg hjhd htzh';
$smtpPort = 587;                   
$smtpSecure = 'tls';                 

$adminEmail = 'serega.riabchenok@yandex.ru';  
$adminName = 'Администратор';

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Метод не поддерживается.";
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo "Пожалуйста, заполните все поля.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Некорректный email.";
    exit;
}

$mail = new PHPMailer(true);

try {
    // Настройки SMTP
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUsername;
    $mail->Password = $smtpPassword;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port = $smtpPort;

    $mail->setFrom($smtpUsername, 'Сайт юридической компании');
    $mail->addAddress($adminEmail, $adminName);

    $mail->Subject = 'Новое сообщение с сайта';
    $mail->Body = "Пользователь: $name\nEmail: $email\n\nСообщение:\n$message";

    $mail->send();

    echo "Спасибо! Ваше сообщение отправлено.";
} catch (Exception $e) {
    http_response_code(500);
    echo "Ошибка при отправке письма: " . $mail->ErrorInfo;
}
