<?php
// Подключение к SQLite
try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Создаём таблицы, если их нет
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS videos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            description TEXT,
            filename TEXT NOT NULL, -- имя файла видео
            thumbnail TEXT,
            views INTEGER DEFAULT 0,
            likes INTEGER DEFAULT 0,
            dislikes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            video_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            likes INTEGER DEFAULT 0,
            dislikes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS video_likes (
            user_id INTEGER NOT NULL,
            video_id INTEGER NOT NULL,
            type INTEGER NOT NULL, -- 1 лайк, -1 дизлайк
            PRIMARY KEY (user_id, video_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS subscriptions (
            subscriber_id INTEGER NOT NULL,
            channel_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (subscriber_id, channel_id),
            FOREIGN KEY (subscriber_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (channel_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");
} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}

session_start();
?>