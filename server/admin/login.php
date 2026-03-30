<?php
session_start();
require_once __DIR__ . '/../../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === ADMIN_USER && password_verify($pass, ADMIN_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #f4f4f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,.1); padding: 2rem; width: 100%; max-width: 360px; }
  h1 { font-size: 1.25rem; margin-bottom: 1.5rem; }
  label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; }
  input { display: block; width: 100%; padding: .5rem .75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; margin-bottom: 1rem; }
  button { width: 100%; padding: .625rem; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
  button:hover { background: #1d4ed8; }
  .error { color: #dc2626; font-size: .875rem; margin-bottom: 1rem; }
</style>
</head>
<body>
<div class="card">
  <h1>Lizard Admin</h1>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus>
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Log in</button>
  </form>
</div>
</body>
</html>
