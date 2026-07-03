<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
require_once 'header.php';
?>
<div class="auth-form">
    <h2>Вход</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
</div>
<?php require_once 'footer.php'; ?>