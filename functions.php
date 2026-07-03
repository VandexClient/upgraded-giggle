<?php
require_once 'config.php';

function getUser($id = null) {
    global $db;
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        return $_SESSION['user'] ?? null;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    $user = getUser();
    return $user && $user['role'] === 'admin';
}

function getVideos($limit = 50) {
    global $db;
    $stmt = $db->query("
        SELECT v.*, u.username 
        FROM videos v 
        JOIN users u ON v.user_id = u.id 
        ORDER BY v.created_at DESC 
        LIMIT $limit
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getVideo($id) {
    global $db;
    $stmt = $db->prepare("
        SELECT v.*, u.username, u.id as author_id 
        FROM videos v 
        JOIN users u ON v.user_id = u.id 
        WHERE v.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function incrementViews($video_id) {
    global $db;
    $db->prepare("UPDATE videos SET views = views + 1 WHERE id = ?")->execute([$video_id]);
}

function likeVideo($video_id, $user_id, $type) {
    global $db;
    // Проверяем, есть ли уже лайк
    $stmt = $db->prepare("SELECT type FROM video_likes WHERE user_id = ? AND video_id = ?");
    $stmt->execute([$user_id, $video_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        if ($existing['type'] == $type) {
            // Отмена
            $db->prepare("DELETE FROM video_likes WHERE user_id = ? AND video_id = ?")->execute([$user_id, $video_id]);
            $field = $type == 1 ? 'likes' : 'dislikes';
            $db->prepare("UPDATE videos SET $field = $field - 1 WHERE id = ?")->execute([$video_id]);
        } else {
            // Смена типа
            $db->prepare("UPDATE video_likes SET type = ? WHERE user_id = ? AND video_id = ?")->execute([$type, $user_id, $video_id]);
            if ($type == 1) {
                $db->prepare("UPDATE videos SET likes = likes + 1, dislikes = dislikes - 1 WHERE id = ?")->execute([$video_id]);
            } else {
                $db->prepare("UPDATE videos SET likes = likes - 1, dislikes = dislikes + 1 WHERE id = ?")->execute([$video_id]);
            }
        }
    } else {
        $db->prepare("INSERT INTO video_likes (user_id, video_id, type) VALUES (?, ?, ?)")->execute([$user_id, $video_id, $type]);
        $field = $type == 1 ? 'likes' : 'dislikes';
        $db->prepare("UPDATE videos SET $field = $field + 1 WHERE id = ?")->execute([$video_id]);
    }
}

function addComment($video_id, $user_id, $content) {
    global $db;
    $stmt = $db->prepare("INSERT INTO comments (video_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$video_id, $user_id, $content]);
}

function getComments($video_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT c.*, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.video_id = ? 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$video_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function uploadVideo($user_id, $title, $description, $file) {
    global $db;
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $filename = time() . '_' . basename($file['name']);
    $target = $upload_dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $db->prepare("INSERT INTO videos (user_id, title, description, filename) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $filename]);
        return $db->lastInsertId();
    }
    return false;
}

function getMyVideos($user_id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM videos WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMyStats($user_id) {
    global $db;
    $videos = $db->prepare("SELECT COUNT(*) as count FROM videos WHERE user_id = ?")->execute([$user_id]);
    $views = $db->prepare("SELECT SUM(views) as sum FROM videos WHERE user_id = ?")->execute([$user_id]);
    $likes = $db->prepare("SELECT SUM(likes) as sum FROM videos WHERE user_id = ?")->execute([$user_id]);
    $subs = $db->prepare("SELECT COUNT(*) as count FROM subscriptions WHERE channel_id = ?")->execute([$user_id]);
    return [
        'videos' => $videos->fetchColumn(),
        'views' => $views->fetchColumn() ?: 0,
        'likes' => $likes->fetchColumn() ?: 0,
        'subscribers' => $subs->fetchColumn() ?: 0,
    ];
}

function getUserVideos($user_id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM videos WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Админские функции
function getAllUsers() {
    global $db;
    return $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}

function setUserRole($user_id, $role) {
    global $db;
    $db->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $user_id]);
}

function deleteVideo($video_id) {
    global $db;
    // Удаляем файл
    $stmt = $db->prepare("SELECT filename FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($video && file_exists('uploads/' . $video['filename'])) {
        unlink('uploads/' . $video['filename']);
    }
    $db->prepare("DELETE FROM videos WHERE id = ?")->execute([$video_id]);
}
?>