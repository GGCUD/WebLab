<?php
function fileErrorHandler($errno, $errmsg, $filename, $linenum, $context) {
    $errorTypes = [
        E_USER_ERROR   => 'Критическая ошибка',
        E_USER_WARNING => 'Предупреждение',
        E_USER_NOTICE  => 'Уведомление'
    ];

    $errorEntry = [
        'datetime'    => date("Y-m-d H:i:s"),
        'errno'       => $errno,
        'errtype'     => $errorTypes[$errno] ?? 'Неизвестный тип',
        'errmsg'      => $errmsg,
        'script'      => $filename,
        'line'        => $linenum,
        'context'     => $context
    ];

    // Логирование в файл (пример)
    error_log(json_encode($errorEntry, JSON_PRETTY_PRINT), 3, 'file_errors.log');
    
    // Для демонстрации выводим массив
    echo "<pre>Произошла ошибка:\n";
    print_r($errorEntry);
    echo "</pre>";
}

function safeFileCopy($source, $dest) {
    if (!file_exists($source)) {
        trigger_error("Исходный файл не существует", E_USER_ERROR);
        return false;
    }
    
    if (file_exists($dest)) {
        trigger_error("Целевой файл уже существует", E_USER_WARNING);
        return false;
    }
    
    if (!copy($source, $dest)) {
        trigger_error("Ошибка копирования файла", E_USER_ERROR);
        return false;
    }
    
    return true;
}

// Устанавливаем обработчик
set_error_handler("fileErrorHandler", E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

// Тестирование
// 1. Ошибка: исходный файл не существует
//safeFileCopy("nonexistent.txt", "copy.txt");

// 2. Предупреждение: целевой файл существует
//file_put_contents("existing.txt", "test");
//safeFileCopy("existing.txt", "existing.txt");

// 3. Успешное копирование
//safeFileCopy("source.txt", "destination.txt");
?>