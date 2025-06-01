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
    
    <form action="save_news.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $news['id'] ?>">
        
        <div class="form-group">
            <label>Заголовок:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($news['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Краткое описание:</label>
            <textarea name="excerpt" required><?= htmlspecialchars($news['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Полный текст:</label>
            <textarea name="content" rows="6" required><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Теги (через запятую):</label>
            <input type="text" name="tags" value="<?= htmlspecialchars(implode(', ', $news['tags'] ?? [])) ?>">
        </div>

        <div class="form-group">
            <label>Изображение:</label>
            <input type="file" name="image" accept="image/*">
            <?php if(!empty($news['image'])): ?>
                <img src="<?= htmlspecialchars($news['image']) ?>" style="max-width: 200px; margin-top: 10px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="save-btn">Сохранить</button>
        <a href="admin_news.php" class="cancel-btn">Отмена</a>
    </form>
</div>

<?php include 'footer.php'; ?>