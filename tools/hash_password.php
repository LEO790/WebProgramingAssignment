<?php

/**
 * Generate a bcrypt hash for use in raw SQL (e.g. inserting an admin in phpMyAdmin).
 *
 * Usage from project folder (XAMPP PHP):
 *   C:\xampp\php\php.exe tools\hash_password.php "YourStrongPassword"
 */

declare(strict_types=1);

$pwd = $argv[1] ?? null;
if ($pwd === null || $pwd === '') {
    fwrite(STDERR, "Usage: php tools/hash_password.php \"YourPassword\"\n");
    exit(1);
}

echo password_hash($pwd, PASSWORD_DEFAULT), PHP_EOL;
