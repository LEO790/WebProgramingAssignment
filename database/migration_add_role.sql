-- Run once if your `users` table was created before the `role` column existed.
-- In phpMyAdmin: select database `web_programing_assignment`, then SQL tab.

ALTER TABLE users
    ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
    AFTER full_name;

-- Make an existing account an admin (change the username):
-- UPDATE users SET role = 'admin' WHERE username = 'your_username';
