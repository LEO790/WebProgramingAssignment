<?php
declare(strict_types=1);
$pageTitle = 'Sign up · Vecna online clothing store';
require_once __DIR__ . '/includes/header.php';

$message = '';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? (string) $_POST['username'] : '';
    $email = isset($_POST['email']) ? (string) $_POST['email'] : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $fullName = isset($_POST['full_name']) ? (string) $_POST['full_name'] : '';

    $result = auth_register($username, $email, $password, $fullName);
    $message = $result['message'];
    $ok = $result['success'];
}
?>
<div class="card">
    <h1>Create account</h1>
    <p class="sub">Passwords are stored using PHP <code>password_hash()</code> (bcrypt).</p>

    <?php if ($message !== ''): ?>
        <div class="msg <?= $ok ? 'ok' : 'err' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" action="register.php" autocomplete="off">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required maxlength="50"
               value="<?= isset($_POST['username']) ? e((string) $_POST['username']) : '' ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?= isset($_POST['email']) ? e((string) $_POST['email']) : '' ?>">

        <label for="full_name">Full name (optional)</label>
        <input type="text" id="full_name" name="full_name" maxlength="100"
               value="<?= isset($_POST['full_name']) ? e((string) $_POST['full_name']) : '' ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8"
               autocomplete="new-password">

        <button type="submit" class="btn">Sign up</button>
    </form>
    <p class="muted" style="margin-top:1rem;">Already have an account? <a href="login.php">Log in</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
