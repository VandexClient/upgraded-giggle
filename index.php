<?php require_once 'header.php'; ?>
<div class="video-grid">
    <?php
    $videos = getVideos();
    if (count($videos) === 0): ?>
        <div class="empty-state">
            <h2>Пока нет видео</h2>
            <p>Будьте первым, кто загрузит видео!</p>
        </div>
    <?php else: ?>
        <?php foreach ($videos as $video): ?>
            <div class="video-card">
                <a href="watch.php?id=<?= $video['id'] ?>">
                    <div class="thumbnail">
                        <img src="<?= $video['thumbnail'] ?? 'default.jpg' ?>" alt="<?= htmlspecialchars($video['title']) ?>">
                        <span class="duration">0:00</span>
                    </div>
                    <h3><?= htmlspecialchars($video['title']) ?></h3>
                    <p class="channel"><?= htmlspecialchars($video['username']) ?></p>
                    <p class="meta"><?= $video['views'] ?> просмотров</p>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>