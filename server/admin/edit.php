<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../../config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$lizard = null;
$error = '';

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM lizards WHERE id = ?');
    $stmt->execute([$id]);
    $lizard = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$lizard) {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'name'         => trim($_POST['name'] ?? ''),
        'species'      => trim($_POST['species'] ?? ''),
        'morph'        => trim($_POST['morph'] ?? ''),
        'locality'     => trim($_POST['locality'] ?? ''),
        'gender'       => trim($_POST['gender'] ?? ''),
        'date_of_birth'=> trim($_POST['date_of_birth'] ?? ''),
        'sire'         => trim($_POST['sire'] ?? ''),
        'dame'         => trim($_POST['dame'] ?? ''),
        'weight_g'     => $_POST['weight_g'] !== '' ? (float)$_POST['weight_g'] : null,
        'price'        => $_POST['price'] !== '' ? (float)$_POST['price'] : null,
        'available'    => isset($_POST['available']) ? 1 : 0,
        'description'  => trim($_POST['description'] ?? ''),
        'photo'        => trim($_POST['photo'] ?? ''),
        'obtained_from'=> trim($_POST['obtained_from'] ?? ''),
    ];

    if ($fields['name'] === '') {
        $error = 'Name is required.';
    } else {
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $stmt = $pdo->prepare("UPDATE lizards SET $set WHERE id = ?");
            $stmt->execute([...array_values($fields), $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt = $pdo->prepare("INSERT INTO lizards ($cols) VALUES ($placeholders)");
            $stmt->execute(array_values($fields));
        }
        header('Location: index.php');
        exit;
    }
}

$v = $lizard ?? array_fill_keys([
    'name','species','morph','locality','gender','date_of_birth',
    'sire','dame','weight_g','price','available','description','photo','obtained_from'
], '');
$title = $id ? 'Edit Lizard' : 'Add Lizard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title ?> — Admin</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #f4f4f5; padding: 2rem; }
  .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.08); padding: 2rem; max-width: 720px; }
  h1 { font-size: 1.25rem; margin-bottom: 1.5rem; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .full { grid-column: 1 / -1; }
  label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; color: #374151; }
  input[type=text], input[type=number], input[type=date], select, textarea {
    display: block; width: 100%; padding: .5rem .75rem; border: 1px solid #d1d5db;
    border-radius: 6px; font-size: .9rem; font-family: inherit;
  }
  textarea { resize: vertical; min-height: 80px; }
  .checkbox-row { display: flex; align-items: center; gap: .5rem; padding-top: 1.5rem; }
  .checkbox-row input { width: auto; }
  .footer { display: flex; gap: .75rem; margin-top: 1.5rem; }
  .btn { padding: .5rem 1.25rem; border-radius: 6px; font-size: .9rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; }
  .btn-primary { background: #2563eb; color: #fff; }
  .btn-primary:hover { background: #1d4ed8; }
  .btn-cancel { background: #fff; color: #374151; border: 1px solid #d1d5db; }
  .btn-cancel:hover { background: #f9fafb; }
  .error { color: #dc2626; font-size: .875rem; margin-bottom: 1rem; }
</style>
</head>
<body>
<div class="card">
  <h1><?= $title ?></h1>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post">
    <div class="grid">
      <div>
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($v['name']) ?>" required>
      </div>
      <div>
        <label for="species">Species</label>
        <input type="text" id="species" name="species" value="<?= htmlspecialchars($v['species']) ?>">
      </div>
      <div>
        <label for="morph">Morph</label>
        <input type="text" id="morph" name="morph" value="<?= htmlspecialchars($v['morph']) ?>">
      </div>
      <div>
        <label for="locality">Locality</label>
        <input type="text" id="locality" name="locality" value="<?= htmlspecialchars($v['locality']) ?>">
      </div>
      <div>
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
          <option value="">Unknown</option>
          <?php foreach (['Male','Female'] as $g): ?>
            <option value="<?= $g ?>" <?= $v['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="date_of_birth">Date of Birth</label>
        <input type="text" id="date_of_birth" name="date_of_birth" placeholder="e.g. 2023-04-15"
               value="<?= htmlspecialchars($v['date_of_birth']) ?>">
      </div>
      <div>
        <label for="sire">Sire (father name)</label>
        <input type="text" id="sire" name="sire" value="<?= htmlspecialchars($v['sire']) ?>">
      </div>
      <div>
        <label for="dame">Dame (mother name)</label>
        <input type="text" id="dame" name="dame" value="<?= htmlspecialchars($v['dame']) ?>">
      </div>
      <div>
        <label for="weight_g">Weight (g)</label>
        <input type="number" id="weight_g" name="weight_g" step="0.1"
               value="<?= $v['weight_g'] !== null && $v['weight_g'] !== '' ? htmlspecialchars($v['weight_g']) : '' ?>">
      </div>
      <div>
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01"
               value="<?= $v['price'] !== null && $v['price'] !== '' ? htmlspecialchars($v['price']) : '' ?>">
      </div>
      <div>
        <label for="photo">Photo URL</label>
        <input type="text" id="photo" name="photo" value="<?= htmlspecialchars($v['photo']) ?>">
      </div>
      <div>
        <label for="obtained_from">Obtained From</label>
        <input type="text" id="obtained_from" name="obtained_from" value="<?= htmlspecialchars($v['obtained_from']) ?>">
      </div>
      <div class="full">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($v['description']) ?></textarea>
      </div>
      <div class="checkbox-row">
        <input type="checkbox" id="available" name="available" <?= $v['available'] ? 'checked' : '' ?>>
        <label for="available" style="margin:0">Available for sale</label>
      </div>
    </div>
    <div class="footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="index.php" class="btn btn-cancel">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
