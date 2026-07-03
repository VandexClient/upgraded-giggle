<?php
require_once 'functions.php';
$user = getUser();
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawTube</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400..700&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="container header-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon">▶</span>
            <span class="logo-text">Raw<span>Tube</span></span>
        </a>
        <form class="search-form" method="get" action="search.php">
            <input type="text" name="q" placeholder="Поиск..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">🔍</button>
        </form>
        <nav class="nav">
            <?php if ($user): ?>
                <a href="upload.php" class="btn-primary">Загрузить</a>
                <a href="studio.php">Студия</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php" class="admin-link">Админ</a>
                <?php endif; ?>
                <a href="profile.php"><?= htmlspecialchars($user['username']) ?></a>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Вход</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">