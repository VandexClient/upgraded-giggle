<?php
require_once 'header.php';
if (!$user) {
    header('Location: login.php');
    exit;
}
$videos = getMyVideos($user['id']);
$stats = getMyStats($user['id']);
?>
<div class="studio">
    <h1>Студия</h1>
    <div class="stats-grid">
        <div class="stat-card">Видео: <?= $stats['videos'] ?></div>
        <div class="stat-card">Просмотры: <?= $stats['views'] ?></div>
        <div class="stat-card">Лайки: <?= $stats['likes'] ?></div>
        <div class="stat-card">Подписчики: <?= $stats['subscribers'] ?></div>
    </div>
    <h2>Мои видео</h2>
    <div class="video-grid">
        <?php foreach ($videos as $v): ?>
            <div class="video-card">
                <a href="watch.php?id=<?= $v['id'] ?>">
                    <div class="thumbnail">
                        <img src="<?= $v['thumbnail'] ?? 'default.jpg' ?>" alt="">
                    </div>
                    <h3><?= htmlspecialchars($v['title']) ?></h3>
                    <p class="meta"><?= $v['views'] ?> просмотров</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once 'footer.php'; ?>