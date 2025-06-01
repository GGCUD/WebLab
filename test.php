<?php
// Подключаем наш обработчик ошибок и функции
require_once 'file_unctions.php'; // Файл из предыдущего примера

// Устанавливаем пользовательский обработчик ошибок
set_error_handler("fileErrorHandler", E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тестирование обработки файловых ошибок</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f0f0f0;
        }
        .test-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .result {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .error {
            background: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .warning {
            background: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
        }
        form {
            margin: 20px 0;
        }
        input[type="text"] {
            width: 300px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h1>Тестирование файловых операций</h1>

    <div class="test-section">
        <h2>Ручное тестирование</h2>
        <form method="post">
            <p>
                Исходный файл: 
                <input type="text" name="source" value="test_source.txt" required>
            </p>
            <p>
                Целевой файл: 
                <input type="text" name="dest" value="test_dest.txt" required>
            </p>
            <button type="submit">Выполнить копирование</button>
        </form>

        <?php
        // Обработка формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $source = htmlspecialchars($_POST['source']);
            $dest = htmlspecialchars($_POST['dest']);
            
            echo '<div class="result">';
            echo "<h3>Результат операции:</h3>";
            
            // Вызываем нашу функцию копирования
            if (safeFileCopy($source, $dest)) {
                echo '<div class="success">Файл успешно скопирован!</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>Тесты</h2>
        
        <?php
        // Тест 1: Несуществующий исходный файл
        echo '<div class="result">';
        echo "<h4>Тест 1: Копирование несуществующего файла</h4>";
        safeFileCopy("non_existent.txt", "copy1.txt");
        echo '</div>';

        // Тест 2: Существующий целевой файл
        echo '<div class="result">';
        echo "<h4>Тест 2: Целевой файл существует</h4>";
        file_put_contents("existing.txt", "test");
        safeFileCopy("existing.txt", "existing.txt");
        echo '</div>';

        // Тест 3: Успешное копирование
        echo '<div class="result">';
        echo "<h4>Тест 3: Успешное копирование</h4>";
        file_put_contents("valid_source.txt", "test content");
        safeFileCopy("valid_source.txt", "valid_dest.txt");
        if (file_exists("valid_dest.txt")) {
            echo '<div class="success">Тест пройден успешно!</div>';
        }
        echo '</div>';
        ?>
    </div>

    <div class="test-section">
        <h2>Лог ошибок</h2>
        <pre><?php 
            echo file_exists('file_errors.log') ? 
                htmlspecialchars(file_get_contents('file_errors.log')) : 
                'Лог пуст'; 
        ?></pre>
    </div>
</body>
</html>