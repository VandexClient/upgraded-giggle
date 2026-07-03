<?php
require_once 'header.php';
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_role'])) {
        setUserRole($_POST['user_id'], $_POST['role']);
    }
    if (isset($_POST['delete_video'])) {
        deleteVideo($_POST['video_id']);
    }
    header('Location: admin.php');
    exit;
}

$users = getAllUsers();
$videos = getVideos(100);
?>
<div class="admin-panel">
    <h1>Админ-панель</h1>
    <h2>Пользователи</h2>
    <table>
        <tr><th>ID</th><th>Имя</th><th>Email</th><th>Роль</th><th>Действия</th></tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <select name="role" onchange="this.form.submit()">
                            <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option>
                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                        </select>
                        <input type="hidden" name="set_role" value="1">
                    </form>
                </td>
                <td><a href="profile.php?id=<?= $u['id'] ?>">Профиль</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h2>Все видео</h2>
    <table>
        <tr><th>ID</th><th>Название</th><th>Автор</th><th>Действия</th></tr>
        <?php foreach ($videos as $v): ?>
            <tr>
                <td><?= $v['id'] ?></td>
                <td><?= htmlspecialchars($v['title']) ?></td>
                <td><?= htmlspecialchars($v['username']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Удалить видео?')">
                        <input type="hidden" name="video_id" value="<?= $v['id'] ?>">
                        <button type="submit" name="delete_video" value="1" class="danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require_once 'footer.php'; ?>