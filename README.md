# Vecna online clothing store — project notes

PHP + MySQL site with user accounts (signup, login, profile) and an admin role. Use this README to see what each file is for.

---

## Requirements

- **XAMPP** (or similar): Apache + PHP + MySQL  
- PHP **PDO** extension enabled (default in XAMPP)  
- Browser access to `http://localhost/...` (adjust path if your folder name differs)

---

## Quick setup (for collaborators)

1. Copy the project into `htdocs` (or your web root).  
2. Start **Apache** and **MySQL** in XAMPP.  
3. Create the database and table: run `database/create_database.sql`, then `database/schema.sql`, in phpMyAdmin (SQL tab), or import in order.  
4. If the database already existed **without** the `role` column, run `database/migration_add_role.sql` once.  
5. Edit **`config/config.php`** if your MySQL user, password, or database name is not the default.  
6. Open **`index.php`** in the browser using your local URL (example:  
   `http://localhost/GroupAssignment/WebProgramingAssignment/`).

---

## Project layout — what each file does

### Pages (site root)

| File | Purpose |
|------|---------|
| **`index.php`** | Home page. Welcome text and links to sign up / log in, or to profile when logged in. |
| **`register.php`** | Sign-up form: username, email, optional full name, password. New users get `role = user`. Passwords are stored with `password_hash()` (bcrypt). |
| **`login.php`** | Login form: username **or** email + password. Starts a PHP session and redirects to profile on success. Same page is used for **admin** — admin is not a separate URL. |
| **`logout.php`** | Clears the session and redirects to the home page. |
| **`profile.php`** | Logged-in only. Shows account details and a form to update full name and optional new password (requires current password). |
| **`admin.php`** | Logged-in **and** `role = admin` only. Placeholder for future store management (products, orders, etc.). Others are redirected to the home page. |

### `includes/` — shared PHP

| File | Purpose |
|------|---------|
| **`header.php`** | Loads helpers + auth, starts the session, prints `<head>`, site title, CSS link, top navigation (Vecna branding, Home, Admin/Profile/Log out or Log in/Sign up). |
| **`footer.php`** | Closes the main wrapper and `</html>`. |
| **`helpers.php`** | **`e()`** — escapes output for HTML (helps avoid XSS). |
| **`db.php`** | Returns a single PDO connection to MySQL (reads from `config/config.php`). |
| **`auth.php`** | Core auth logic: **register**, **login**, **logout**, **profile update**; **`auth_require_login()`**, **`auth_require_admin()`**; **`auth_current_user()`**, **`auth_is_admin()`**. Uses prepared statements and `password_hash` / `password_verify`. |

### `config/`

| File | Purpose |
|------|---------|
| **`config.php`** | Database host, database name, MySQL user, password, charset. **Each developer should edit this** to match their local MySQL. |

### `assets/`

| Path | Purpose |
|------|---------|
| **`assets/css/style.css`** | Global styles for the site (layout, forms, cards, messages). |

### `database/` — SQL

| File | Purpose |
|------|---------|
| **`create_database.sql`** | Creates the `web_programing_assignment` database (utf8mb4). Run before other SQL if the DB does not exist. |
| **`schema.sql`** | Creates the **`users`** table (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `created_at`). Use for a fresh install. |
| **`migration_add_role.sql`** | **Only if** you already had a `users` table **without** `role`. Adds the `role` column; includes a commented example to promote a user to admin. |

### `tools/` — utilities

| File | Purpose |
|------|---------|
| **`hash_password.php`** | CLI helper: generates a bcrypt hash for a password. Use when you need to **set or reset** a password directly in SQL (e.g. emergency admin reset). **Do not** expose this on a public server without protection. Example: `C:\xampp\php\php.exe tools\hash_password.php "YourPassword"` |

---

## Admin accounts (short summary)

- **Registration** always creates **`role = user`**. There is no public “sign up as admin”.  
- To make someone admin: in phpMyAdmin run  
  `UPDATE users SET role = 'admin' WHERE username = 'their_username';`  
- Log in on **`login.php`** with that account’s **real password** (the one from signup, or a password you set after resetting `password_hash` with `hash_password.php`).  
- After login, **`admin.php`** appears in the nav for admins.

---

## Security notes (for the report / team)

- Passwords: **hashed** with PHP `password_hash()` / `password_verify()`, not stored in plain text.  
- SQL: **prepared statements** (PDO) to reduce injection risk.  
- Sessions: `session_regenerate_id()` on login.  
- Output: use **`e()`** in templates when showing user-controlled text.

