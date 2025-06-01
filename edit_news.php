<?php
require_once 'admin_header.php';

$news = ['id' => uniqid(), 'date' => date('Y-m-d H:i:s')];
$news_dir = __DIR__.'/news/';

if(isset($_GET['id'])) {
    $file = $news_dir.'news_'.$_GET['id'].'.json';
    if(file_exists($file)) {
        $news = json_decode(file_get_contents($file), true);
    }
}
?>

<div class="admin-content">
    <h1><?= isset($_GET['id']) ? 'Редактирование' : 'Создание' ?> новости</h1>
    
    <form action="save_news.php" method="post" enctype="multipart/form-data" class="news-form">
        <input type="hidden" name="id" value="<?= $news['id'] ?>">
        
        <div class="form-group">
            <label>Заголовок:</label>
            <input type="text" name="title" 
                   value="<?= htmlspecialchars($news['title'] ?? '') ?>" 
                   class="large-input"
                   required>
        </div>

        <div class="form-group">
            <label>Краткое описание:</label>
            <textarea name="excerpt" 
                      class="large-textarea medium-height"
                      required><?= htmlspecialchars($news['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Полный текст:</label>
            <textarea name="content" 
                      class="large-textarea tall-height"
                      required><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Теги (через запятую):</label>
            <input type="text" name="tags" 
                   value="<?= htmlspecialchars(implode(', ', $news['tags'] ?? [])) ?>"
                   class="large-input">
        </div>

        <div class="form-group">
            <label>Изображение:</label>
            <div class="image-upload">
                <input type="file" name="image" accept="image/*" class="file-input">
                <?php if(!empty($news['image'])): ?>
                    <img src="<?= htmlspecialchars($news['image']) ?>" 
                         class="preview-image">
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Сохранить</button>
            <a href="admin_news.php" class="cancel-btn">Отмена</a>
        </div>
    </form>
</div>

<style>
    /* Общие стили для формы */
    .news-form {
        max-width: 800px;
        margin: 0 auto;
    }

    /* Стили для всех полей ввода */
    .large-input {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 8px 0;
    }

    /* Базовые стили для текстовых областей */
    .large-textarea {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 8px 0;
        resize: vertical;
    }

    /* Высота для разных текстовых областей */
    .medium-height {
        min-height: 120px;
    }

    .tall-height {
        min-height: 250px;
    }

    /* Стили для загрузки изображений */
    .image-upload {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .preview-image {
        max-width: 300px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .file-input {
        padding: 10px;
        background: #f8f8f8;
        border: 1px dashed #ccc;
        border-radius: 4px;
    }

    /* Кнопки управления */
    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 15px;
    }

    .save-btn, .cancel-btn {
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .save-btn {
        background: #4CAF50;
        color: white;
        border: none;
    }

    .cancel-btn {
        background: #666;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .image-upload {
            flex-direction: column;
        }
        
        .preview-image {
            max-width: 100%;
        }
    }
</style>

<?php include 'footer.php'; ?>