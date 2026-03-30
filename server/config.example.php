<?php
// Copy this file to config.php and fill in your values.
// config.php is gitignored — never commit real credentials.

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Generate a hash with: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
// Or use: https://www.browserling.com/tools/bcrypt
define('ADMIN_USER', 'admin');
define('ADMIN_HASH', '$2y$10$REPLACE_THIS_WITH_A_REAL_BCRYPT_HASH');
