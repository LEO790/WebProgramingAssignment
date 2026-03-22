<?php
declare(strict_types=1);
$pageTitle = 'Your profile · Vecna online clothing store';
require_once __DIR__ . '/includes/header.php';
auth_require_login();

$user = auth_current_user();
if ($user === null) {
    auth_logout();
    header('Location: login.php');
    exit;
}

$message = '';
$ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = isset($_POST['full_name']) ? (string) $_POST['full_name'] : '';
    $currentPassword = isset($_POST['current_password']) ? (string) $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? (string) $_POST['new_password'] : null;

    $result = auth_update_profile(
        (int) $user['id'],
        $fullName,
        $newPassword,
        $currentPassword
    );
    $message = $result['message'];
    $ok = $result['success'];
    if ($ok) {
        $user = auth_current_user();
    }
}
?>
<div class="card">
    <h1>Profile</h1>
    <p class="sub">Signed in as <strong><?= e($user['username']) ?></strong>.</p>

    <?php if ($message !== ''): ?>
        <div class="msg <?= $ok ? 'ok' : 'err' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <h2 class="muted" style="font-size:1rem;font-weight:600;margin:0 0 0.75rem;">Account details</h2>
    <div class="profile-row"><span>Username</span><span><?= e($user['username']) ?></span></div>
    <div class="profile-row"><span>Email</span><span><?= e($user['email']) ?></span></div>
    <div class="profile-row"><span>Full name</span><span><?= $user['full_name'] !== null && $user['full_name'] !== '' ? e($user['full_name']) : '—' ?></span></div>

    <hr class="soft">

    <h2 class="muted" style="font-size:1rem;font-weight:600;margin:0 0 0.75rem;">Update profile</h2>
    <form method="post" action="profile.php" autocomplete="off">
        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" maxlength="100"
               value="<?= e((string) ($user['full_name'] ?? '')) ?>">

        <p class="muted" style="margin:0 0 0.5rem;">Leave new password blank to keep your current password.</p>
        <label for="new_password">New password (optional)</label>
        <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password">

        <label for="current_password">Current password (required to save changes)</label>
        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">

        <button type="submit" class="btn">Save changes</button>
    </form>

    <hr class="soft">

    <p class="muted" style="margin:0;">
        <a href="logout.php">Log out</a>
    </p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
