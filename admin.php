<?php
declare(strict_types=1);
$pageTitle = 'Admin · Vecna online clothing store';
require_once __DIR__ . '/includes/header.php';
auth_require_admin();
?>
<div class="card">
    <h1>Admin</h1>
    <p class="sub">You are signed in as an administrator. Add your store management tools here (products, orders, etc.).</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
