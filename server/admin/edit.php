<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../../config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = $_GET['action'] ?? '';
$error  = '';

// ── Helpers ────────────────────────────────────────────────────────────────

function getLizard(PDO $pdo, int $id): array|false {
    $s = $pdo->prepare('SELECT * FROM lizards WHERE id = ?');
    $s->execute([$id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}

function getImages(PDO $pdo, int $id): array {
    $s = $pdo->prepare('SELECT * FROM lizard_images WHERE lizard_id = ? ORDER BY sort_order, id');
    $s->execute([$id]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

function getWeights(PDO $pdo, int $id): array {
    $s = $pdo->prepare('SELECT id, weighed_on, weight_g FROM lizard_weights WHERE lizard_id = ? ORDER BY weighed_on ASC');
    $s->execute([$id]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

// ── Action: add a weight entry ─────────────────────────────────────────────

if ($action === 'add_weight' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $weighed_on = trim($_POST['weighed_on'] ?? '');
    $weight_g   = $_POST['weight_g'] !== '' ? (float)$_POST['weight_g'] : null;
    if ($weighed_on && $weight_g !== null && $weight_g > 0) {
        $s = $pdo->prepare(
            'INSERT INTO lizard_weights (lizard_id, weighed_on, weight_g) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE weight_g = VALUES(weight_g)'
        );
        $s->execute([$id, $weighed_on, $weight_g]);
    }
    header("Location: edit.php?id=$id");
    exit;
}

// ── Action: delete a weight entry ──────────────────────────────────────────

if ($action === 'delete_weight' && $id) {
    $weightId = (int)($_GET['weight_id'] ?? 0);
    if ($weightId) {
        $s = $pdo->prepare('DELETE FROM lizard_weights WHERE id = ? AND lizard_id = ?');
        $s->execute([$weightId, $id]);
    }
    header("Location: edit.php?id=$id");
    exit;
}

// ── Action: delete a single image ──────────────────────────────────────────

if ($action === 'delete_image' && $id) {
    $imageId = (int)($_GET['image_id'] ?? 0);
    if ($imageId) {
        $s = $pdo->prepare('SELECT filename FROM lizard_images WHERE id = ? AND lizard_id = ?');
        $s->execute([$imageId, $id]);
        $img = $s->fetch(PDO::FETCH_ASSOC);
        if ($img) {
            $file = __DIR__ . '/../images/' . $img['filename'];
            if (file_exists($file)) unlink($file);
            $pdo->prepare('DELETE FROM lizard_images WHERE id = ?')->execute([$imageId]);
        }
    }
    header("Location: edit.php?id=$id");
    exit;
}

// ── Action: upload images ──────────────────────────────────────────────────

if ($action === 'upload_image' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $lizard = getLizard($pdo, $id);
    if (!$lizard) { header('Location: index.php'); exit; }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadDir = __DIR__ . '/../images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $files = $_FILES['images'] ?? [];
    $count = is_array($files['name']) ? count($files['name']) : 0;

    $s = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM lizard_images WHERE lizard_id = ?');
    $s->execute([$id]);
    $sortOrder = (int)$s->fetchColumn() + 1;

    $insert = $pdo->prepare('INSERT INTO lizard_images (lizard_id, filename, sort_order) VALUES (?, ?, ?)');

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (!in_array($files['type'][$i], $allowed)) continue;

        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $filename = uniqid('lizard_', true) . '.' . $ext;
        if (move_uploaded_file($files['tmp_name'][$i], $uploadDir . $filename)) {
            $insert->execute([$id, $filename, $sortOrder++]);
        }
    }

    header("Location: edit.php?id=$id");
    exit;
}

// ── Action: save fields ────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'upload_image') {
    $fields = [
        'name'          => trim($_POST['name'] ?? ''),
        'species'       => trim($_POST['species'] ?? ''),
        'morph'         => trim($_POST['morph'] ?? ''),
        'locality'      => trim($_POST['locality'] ?? ''),
        'gender'        => trim($_POST['gender'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'sire'          => trim($_POST['sire'] ?? ''),
        'dame'          => trim($_POST['dame'] ?? ''),
        'weight_g'      => $_POST['weight_g'] !== '' ? (float)$_POST['weight_g'] : null,
        'price'         => $_POST['price']    !== '' ? (float)$_POST['price']    : null,
        'available'     => isset($_POST['available']) ? 1 : 0,
        'description'   => trim($_POST['description'] ?? ''),
        'obtained_from' => trim($_POST['obtained_from'] ?? ''),
    ];

    if ($fields['name'] === '') {
        $error = 'Name is required.';
    } else {
        if ($id) {
            $set  = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $stmt = $pdo->prepare("UPDATE lizards SET $set WHERE id = ?");
            $stmt->execute([...array_values($fields), $id]);
        } else {
            $cols         = implode(', ', array_keys($fields));
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt         = $pdo->prepare("INSERT INTO lizards ($cols) VALUES ($placeholders)");
            $stmt->execute(array_values($fields));
            $id = (int)$pdo->lastInsertId();
        }
        header("Location: edit.php?id=$id");
        exit;
    }
}

// ── Load data for display ──────────────────────────────────────────────────

$lizard = $id ? getLizard($pdo, $id) : null;
if ($id && !$lizard) { header('Location: index.php'); exit; }
$images  = $id ? getImages($pdo, $id) : [];
$weights = $id ? getWeights($pdo, $id) : [];

$v     = $lizard ?? array_fill_keys(['name','species','morph','locality','gender','date_of_birth','sire','dame','weight_g','price','available','description','obtained_from'], '');
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
  .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.08); padding: 2rem; max-width: 720px; margin-bottom: 1.5rem; }
  h1 { font-size: 1.25rem; margin-bottom: 1.5rem; }
  h2 { font-size: 1rem; margin-bottom: 1rem; color: #374151; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .full { grid-column: 1 / -1; }
  label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; color: #374151; }
  input[type=text], input[type=number], select, textarea {
    display: block; width: 100%; padding: .5rem .75rem;
    border: 1px solid #d1d5db; border-radius: 6px; font-size: .9rem; font-family: inherit;
  }
  textarea { resize: vertical; min-height: 80px; }
  .checkbox-row { display: flex; align-items: center; gap: .5rem; padding-top: 1.5rem; }
  .checkbox-row input { width: auto; }
  .footer { display: flex; gap: .75rem; margin-top: 1.5rem; }
  .btn { padding: .5rem 1.25rem; border-radius: 6px; font-size: .9rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; display: inline-block; }
  .btn-primary  { background: #2563eb; color: #fff; }
  .btn-primary:hover  { background: #1d4ed8; }
  .btn-cancel   { background: #fff; color: #374151; border: 1px solid #d1d5db; }
  .btn-cancel:hover   { background: #f9fafb; }
  .btn-upload   { background: #059669; color: #fff; }
  .btn-upload:hover   { background: #047857; }
  .error { color: #dc2626; font-size: .875rem; margin-bottom: 1rem; }

  /* Image gallery */
  .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .image-item { position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; background: #f9fafb; }
  .image-item img { width: 100%; height: 120px; object-fit: cover; display: block; }
  .image-item .delete-btn {
    position: absolute; top: 4px; right: 4px;
    background: rgba(220,38,38,.85); color: #fff; border: none; border-radius: 4px;
    padding: 2px 7px; font-size: .75rem; cursor: pointer; font-weight: 600;
  }
  .image-item .delete-btn:hover { background: #dc2626; }
  .upload-zone { border: 2px dashed #d1d5db; border-radius: 8px; padding: 1.5rem; text-align: center; color: #6b7280; font-size: .875rem; }
  .upload-zone input[type=file] { margin-top: .75rem; }
  .empty-images { color: #9ca3af; font-size: .875rem; margin-bottom: 1rem; }
  .note { font-size: .8rem; color: #9ca3af; margin-top: .5rem; }

  /* Weight history table */
  .weight-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; font-size: .9rem; }
  .weight-table th { text-align: left; font-size: .8rem; color: #6b7280; font-weight: 600; padding: .4rem .5rem; border-bottom: 1px solid #e5e7eb; }
  .weight-table td { padding: .5rem .5rem; border-bottom: 1px solid #f3f4f6; }
  .weight-table tr:last-child td { border-bottom: none; }
  .btn-danger-sm { background: rgba(220,38,38,.1); color: #dc2626; border: none; border-radius: 4px; padding: 2px 8px; font-size: .75rem; cursor: pointer; font-weight: 600; text-decoration: none; }
  .btn-danger-sm:hover { background: #dc2626; color: #fff; }
  .weight-add { display: grid; grid-template-columns: 1fr 1fr auto; gap: .75rem; align-items: end; margin-top: 1rem; }
</style>
</head>
<body>

<!-- ── Fields form ── -->
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
        <label for="weight_g">Weight (g) <?= $id ? '<span style="font-weight:400;color:#9ca3af">(auto-updated by history)</span>' : '' ?></label>
        <input type="number" id="weight_g" name="weight_g" step="0.1"
               value="<?= $v['weight_g'] !== null && $v['weight_g'] !== '' ? htmlspecialchars((string)$v['weight_g']) : '' ?>"
               <?= $id ? 'readonly style="background:#f9fafb;color:#6b7280"' : '' ?>>
      </div>
      <div>
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01"
               value="<?= $v['price'] !== null && $v['price'] !== '' ? htmlspecialchars((string)$v['price']) : '' ?>">
      </div>
      <div>
        <label for="obtained_from">Obtained From</label>
        <input type="text" id="obtained_from" name="obtained_from" value="<?= htmlspecialchars($v['obtained_from']) ?>">
      </div>
      <div class="checkbox-row">
        <input type="checkbox" id="available" name="available" <?= $v['available'] ? 'checked' : '' ?>>
        <label for="available" style="margin:0">Available for sale</label>
      </div>
      <div class="full">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($v['description']) ?></textarea>
      </div>
    </div>
    <div class="footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="index.php" class="btn btn-cancel">Cancel</a>
    </div>
  </form>
</div>

<!-- ── Images (only shown after lizard exists) ── -->
<?php if ($id): ?>
<div class="card">
  <h2>Photos</h2>

  <?php if (empty($images)): ?>
    <p class="empty-images">No photos yet.</p>
  <?php else: ?>
    <div class="image-grid">
      <?php foreach ($images as $img): ?>
        <div class="image-item">
          <img src="../images/<?= htmlspecialchars($img['filename']) ?>"
               alt=""
               onerror="this.style.opacity='.3'">
          <a href="edit.php?id=<?= $id ?>&action=delete_image&image_id=<?= $img['id'] ?>"
             class="delete-btn"
             onclick="return confirm('Remove this photo?')">✕</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data"
        action="edit.php?id=<?= $id ?>&action=upload_image">
    <div class="upload-zone">
      <div>Upload photos (jpg, png, gif, webp — select multiple)</div>
      <input type="file" name="images[]" multiple accept="image/*">
    </div>
    <div class="footer">
      <button type="submit" class="btn btn-upload">Upload</button>
    </div>
  </form>
</div>
<?php else: ?>
<div class="card">
  <p class="note">Save the lizard first, then you can add photos.</p>
</div>
<?php endif; ?>

<!-- ── Weight History (only shown after lizard exists) ── -->
<?php if ($id): ?>
<div class="card">
  <h2>Weight History</h2>

  <?php if (empty($weights)): ?>
    <p class="empty-images">No weight entries yet.</p>
  <?php else: ?>
    <table class="weight-table">
      <thead>
        <tr><th>Date</th><th>Weight (g)</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($weights as $w): ?>
          <tr>
            <td><?= htmlspecialchars($w['weighed_on']) ?></td>
            <td><?= htmlspecialchars((string)$w['weight_g']) ?>g</td>
            <td>
              <a href="edit.php?id=<?= $id ?>&action=delete_weight&weight_id=<?= $w['id'] ?>"
                 class="btn-danger-sm"
                 onclick="return confirm('Remove this weight entry?')">✕</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <form method="post" action="edit.php?id=<?= $id ?>&action=add_weight">
    <div class="weight-add">
      <div>
        <label>Date</label>
        <input type="date" name="weighed_on" required>
      </div>
      <div>
        <label>Weight (g)</label>
        <input type="number" name="weight_g" step="0.1" min="0.1" required>
      </div>
      <button type="submit" class="btn btn-primary">Add</button>
    </div>
  </form>
</div>
<?php else: ?>
<div class="card">
  <p class="note">Save the lizard first, then you can record weight history.</p>
</div>
<?php endif; ?>

</body>
</html>
