<?php
require_once 'admin_header.php';

$news_dir = __DIR__.'/news/';
$upload_dir = __DIR__.'/images/news/';

// Создаем директории при необходимости
if (!is_dir($news_dir)) mkdir($news_dir, 0755, true);
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? uniqid();
        
        // Обработка изображения
        $image_path = $_POST['old_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'news_'.$id.'.'.$ext;
            $target_path = $upload_dir.$filename;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                throw new Exception('Ошибка загрузки изображения');
            }
            $image_path = '/images/news/'.$filename;
        }

        // Валидация данных
        $required_fields = ['title', 'excerpt', 'content'];
        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field]))) {
                throw new Exception("Заполните поле: ".ucfirst($field));
            }
        }

        // Формируем данные
        $news_data = [
            'id' => $id,
            'title' => htmlspecialchars(trim($_POST['title'])),
            'excerpt' => htmlspecialchars(trim($_POST['excerpt'])),
            'content' => htmlspecialchars(trim($_POST['content'])),
            'tags' => array_filter(array_map('trim', explode(',', $_POST['tags'] ?? ''))),
            'image' => $image_path,
            'date' => date('Y-m-d H:i:s')
        ];

        // Сохраняем в JSON
        $filename = $news_dir.'news_'.$id.'.json';
        if (!file_put_contents($filename, json_encode($news_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            throw new Exception('Ошибка записи файла');
        }

        $_SESSION['success'] = 'Изменения успешно сохранены';
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Гарантированный редирект
header('Location: admin_news.php');
exit;