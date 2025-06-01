<?php
require_once 'admin_header.php';
require_once 'db_connect.php'; // Если нужно, хотя для JSON не требуется

define('NEWS_DIR', __DIR__.'/news/');
$news_files = glob(NEWS_DIR.'news_*.json');
$all_news = [];

foreach($news_files as $file) {
    $news = json_decode(file_get_contents($file), true);
    if($news) {
        $all_news[] = $news;
    }
}

usort($all_news, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>

<div class="admin-content">
    <h1>Управление новостями</h1>
    <a href="edit_news.php" class="add-button">+ Добавить новость</a>

    <div class="news-grid">
        <?php foreach($all_news as $news): ?>
            <div class="news-card">
                <img src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                <h3><?= htmlspecialchars($news['title']) ?></h3>
                <div class="news-actions">
                    <a href="edit_news.php?id=<?= $news['id'] ?>" class="edit-btn">Редактировать</a>
                    <a href="delete_news.php?id=<?= $news['id'] ?>" class="delete-btn" 
                       onclick="return confirm('Удалить эту новость?')">Удалить</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .news-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background: white;
    }

    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .news-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .add-button {
        display: inline-block;
        padding: 10px 20px;
        background: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>
