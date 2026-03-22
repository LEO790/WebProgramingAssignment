<?php
declare(strict_types=1);
$pageTitle = 'Log in · Vecna online clothing store';
require_once __DIR__ . '/includes/header.php';

if (auth_is_logged_in()) {
    header('Location: profile.php');
    exit;
}

$message = '';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = isset($_POST['identifier']) ? (string) $_POST['identifier'] : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    $result = auth_login($identifier, $password);
    $message = $result['message'];
    $ok = $result['success'];
    if ($ok) {
        header('Location: profile.php');
        exit;
    }
}
?>
<div class="card">
    <h1>Log in</h1>
    <p class="sub">Use your username or email. Session is secured with <code>session_regenerate_id()</code> on login.</p>

    <?php if ($message !== ''): ?>
        <div class="msg <?= $ok ? 'ok' : 'err' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off">
        <label for="identifier">Username or email</label>
        <input type="text" id="identifier" name="identifier" required
               value="<?= isset($_POST['identifier']) ? e((string) $_POST['identifier']) : '' ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="btn">Log in</button>
    </form>
    <p class="muted" style="margin-top:1rem;">No account? <a href="register.php">Sign up</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
