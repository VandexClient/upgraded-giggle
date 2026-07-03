<?php
require_once 'header.php';
$id = $_GET['id'] ?? 0;
$video = getVideo($id);
if (!$video) {
    echo "<p>Видео не найдено</p>";
    require_once 'footer.php';
    exit;
}
incrementViews($id);
$comments = getComments($id);
$user = getUser();

// Обработка лайка
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like']) && $user) {
    likeVideo($id, $user['id'], $_POST['like'] == 'like' ? 1 : -1);
    header("Location: watch.php?id=$id");
    exit;
}

// Обработка комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $user) {
    addComment($id, $user['id'], $_POST['comment']);
    header("Location: watch.php?id=$id");
    exit;
}
?>
<div class="watch-container">
    <div class="video-player">
        <video controls src="uploads/<?= $video['filename'] ?>" poster="<?= $video['thumbnail'] ?? 'default.jpg' ?>" width="100%"></video>
    </div>
    <h1><?= htmlspecialchars($video['title']) ?></h1>
    <div class="video-meta">
        <span><?= $video['views'] ?> просмотров</span>
        <span>•</span>
        <span>Автор: <a href="profile.php?id=<?= $video['author_id'] ?>"><?= htmlspecialchars($video['username']) ?></a></span>
    </div>
    <div class="actions">
        <?php if ($user): ?>
            <form method="post" style="display:inline;">
                <button type="submit" name="like" value="like">👍 <?= $video['likes'] ?></button>
                <button type="submit" name="like" value="dislike">👎 <?= $video['dislikes'] ?></button>
            </form>
        <?php else: ?>
            <span>👍 <?= $video['likes'] ?> 👎 <?= $video['dislikes'] ?></span>
        <?php endif; ?>
    </div>
    <div class="description">
        <p><?= nl2br(htmlspecialchars($video['description'])) ?></p>
    </div>

    <div class="comments-section">
        <h3>Комментарии (<?= count($comments) ?>)</h3>
        <?php if ($user): ?>
            <form method="post">
                <textarea name="comment" placeholder="Написать комментарий..." required></textarea>
                <button type="submit">Отправить</button>
            </form>
        <?php else: ?>
            <p>Войдите, чтобы оставить комментарий.</p>
        <?php endif; ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?= htmlspecialchars($comment['username']) ?></strong>
                <span class="time"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once 'footer.php'; ?>