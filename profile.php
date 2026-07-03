<?php
require_once 'header.php';
$user_id = $_GET['id'] ?? ($user ? $user['id'] : 0);
if (!$user_id) {
    header('Location: index.php');
    exit;
}
$profile = getUser($user_id);
if (!$profile) {
    echo "<p>Пользователь не найден</p>";
    require_once 'footer.php';
    exit;
}
$videos = getUserVideos($user_id);
$is_owner = $user && $user['id'] == $user_id;
?>
<div class="profile">
    <div class="profile-header">
        <h1><?= htmlspecialchars($profile['username']) ?></h1>
        <p>Зарегистрирован: <?= date('d.m.Y', strtotime($profile['created_at'])) ?></p>
        <?php if ($is_owner): ?>
            <a href="studio.php" class="btn-primary">Перейти в студию</a>
        <?php endif; ?>
    </div>
    <h2>Видео пользователя (<?= count($videos) ?>)</h2>
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