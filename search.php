<?php
require_once 'header.php';
$q = trim($_GET['q'] ?? '');
$videos = [];
if ($q) {
    global $db;
    $stmt = $db->prepare("
        SELECT v.*, u.username 
        FROM videos v 
        JOIN users u ON v.user_id = u.id 
        WHERE v.title LIKE ? OR v.description LIKE ?
        ORDER BY v.created_at DESC
    ");
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="search-results">
    <h2>Результаты поиска по запросу «<?= htmlspecialchars($q) ?>»</h2>
    <?php if (count($videos) === 0): ?>
        <p>Ничего не найдено.</p>
    <?php else: ?>
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
                <div class="video-card">
                    <a href="watch.php?id=<?= $video['id'] ?>">
                        <div class="thumbnail">
                            <img src="<?= $video['thumbnail'] ?? 'default.jpg' ?>" alt="">
                        </div>
                        <h3><?= htmlspecialchars($video['title']) ?></h3>
                        <p class="channel"><?= htmlspecialchars($video['username']) ?></p>
                        <p class="meta"><?= $video['views'] ?> просмотров</p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>