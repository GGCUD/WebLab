<?php

const NEWS_PER_PAGE = 9;
const NEWS_DIR = __DIR__.'/news/';


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


$page = max(1, (int)($_GET['page'] ?? 1));
$total = count($all_news);
$total_pages = ceil($total / NEWS_PER_PAGE);
$current_news = array_slice($all_news, ($page-1)*NEWS_PER_PAGE, NEWS_PER_PAGE);

include 'header.php'; 
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    .news-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        padding: 120px 20px 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .news-item {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
        color: inherit;
    }

    .news-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .news-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .news-content {
        padding: 20px;
    }

    .news-content h2 {
        font-size: 1.3rem;
        margin: 0 0 10px;
    }

    .news-excerpt {
        color: #555;
        font-size: 0.95rem;
        margin-bottom: 15px;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #888;
    }

    .news-tags span {
        background: #eee;
        border-radius: 4px;
        padding: 2px 6px;
        margin-right: 5px;
        font-size: 0.75rem;
        color: #555;
    }

    /* Пагинация */
    .pagination {
        text-align: center;
        margin: 30px 0;
    }

    .pagination a {
        display: inline-block;
        margin: 0 5px;
        padding: 8px 12px;
        background: #ddd;
        color: #333;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .pagination a:hover {
        background: #bbb;
    }

    .pagination a.active {
        background: #333;
        color: #fff;
        font-weight: bold;
    }
</style>

<!-- Основная разметка -->
<div class="news-list">
    <?php foreach($current_news as $item): ?>
        <article class="news-item">
            <a href="news_single.php?id=<?= $item['id'] ?>">
                <div class="news-image">
                    <img src="<?= htmlspecialchars(ltrim($item['image'], '/')) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>">
                </div>
                <div class="news-content">
                    <h2><?= htmlspecialchars($item['title']) ?></h2>
                    <div class="news-excerpt">
                        <?= htmlspecialchars($item['excerpt']) ?>
                    </div>
                    <div class="news-meta">
                        <div class="news-tags">
                            <?php foreach($item['tags'] as $tag): ?>
                                <span>#<?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </a>
        </article>
    <?php endforeach; ?>
</div>

<!-- Пагинация -->
<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

<?php include 'footer.php'; ?>
