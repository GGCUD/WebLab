<?php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';      
$db_name = 'test';  


$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);


if ($connection->connect_error) {
    die('Ошибка подключения к БД: ' . $connection->connect_error);
}
$connection->set_charset('utf8mb4');?>