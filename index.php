<?php
declare(strict_types=1);
$pageTitle = 'Vecna online clothing store';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card">
    <h1>Welcome</h1>
    <p class="sub">User signup, login, and profile demo for your group assignment.</p>
    <?php if (auth_is_logged_in()): ?>
        <p>You are logged in. Open your <a href="profile.php">profile</a>.</p>
    <?php else: ?>
        <p><a href="register.php">Create an account</a> or <a href="login.php">log in</a>.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
