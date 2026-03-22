<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
auth_start_session();
$pageTitle = $pageTitle ?? 'Vecna online clothing store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">Vecna online clothing store</a>
    <nav class="nav">
        <a href="index.php">Home</a>
        <?php if (auth_is_logged_in()): ?>
            <?php if (auth_is_admin()): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php">Sign up</a>
        <?php endif; ?>
    </nav>
</header>
<div class="wrap">
