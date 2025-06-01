<?php
// Указываем путь к директории с JSON-файлами
define('NEWS_DIR', __DIR__ . '/news/');

// Получаем ID новости из URL
$news_id = (int)($_GET['id'] ?? 0);
$news_file = NEWS_DIR . "news_$news_id.json";

// Если файл не найден — редирект на список новостей
if (!file_exists($news_file)) {
    header('Location: news.php');
    exit;
}

// Получаем данные новости
$news = json_decode(file_get_contents($news_file), true);

include 'header.php';
?>

<article class="full-news">
    <h1><?= htmlspecialchars($news['title']) ?></h1>

    <div class="meta">
        <time><?= date('d.m.Y H:i', strtotime($news['date'])) ?></time>
    </div>

    <?php if (!empty($news['image'])): ?>
        <div class="news-image">
            <img src="<?= htmlspecialchars(ltrim($news['image'], '/')) ?>" 
                 alt="<?= htmlspecialchars($news['title']) ?>">
        </div>
    <?php endif; ?>

    <div class="news-content">
        <?= nl2br(htmlspecialchars($news['content'])) ?>
    </div>

    <a href="news.php" class="back-link">← Все новости</a>
</article>

<?php include 'footer.php'; ?>

<style>
    .full-news {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        font-family: sans-serif;
        line-height: 1.7;
    }

    .full-news h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .meta {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 20px;
    }

    .news-image img {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .news-content {
        font-size: 1.1rem;
        color: #333;
    }

    .back-link {
        display: inline-block;
        margin-top: 30px;
        color: #0066cc;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>
