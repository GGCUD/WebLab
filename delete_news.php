<?php
require_once 'admin_header.php';

$news_dir = __DIR__.'/news/';
$upload_dir = __DIR__.'/uploads/news/';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Удаляем JSON-файл
    $json_file = $news_dir.'news_'.$id.'.json';
    if(file_exists($json_file)) unlink($json_file);
    
    // Удаляем изображение
    $image_files = glob($upload_dir.'news_'.$id.'.*');
    foreach($image_files as $file) {
        if(file_exists($file)) unlink($file);
    }
    
    $_SESSION['message'] = 'Новость успешно удалена';
}

header('Location: admin_news.php');
exit;