<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try {
        $stmt = $db->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->execute([$email, $username, $password]);
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        $error = "Пользователь с таким email или именем уже существует";
    }
}
require_once 'header.php';
?>
<div class="auth-form">
    <h2>Регистрация</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Имя пользователя" required>
        <input type="password" name="password" placeholder="Пароль (мин. 6 символов)" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
</div>
<?php require_once 'footer.php'; ?>