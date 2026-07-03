<?php
require_once 'header.php';
if (!$user) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    if ($title && isset($_FILES['video'])) {
        $video_id = uploadVideo($user['id'], $title, $description, $_FILES['video']);
        if ($video_id) {
            header("Location: watch.php?id=$video_id");
            exit;
        } else {
            $error = "Ошибка загрузки файла";
        }
    } else {
        $error = "Заполните все поля";
    }
}
?>
<div class="upload-form">
    <h2>Загрузить видео</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Название" required>
        <textarea name="description" placeholder="Описание" rows="4"></textarea>
        <input type="file" name="video" accept="video/*" required>
        <button type="submit">Загрузить</button>
    </form>
</div>
<?php require_once 'footer.php'; ?>