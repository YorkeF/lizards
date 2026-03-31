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

function getIsopod(PDO $pdo, int $id): array|false {
    $s = $pdo->prepare('SELECT * FROM isopods WHERE id = ?');
    $s->execute([$id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}

function getIsopodImages(PDO $pdo, int $id): array {
    $s = $pdo->prepare('SELECT * FROM isopod_images WHERE isopod_id = ? ORDER BY sort_order, id');
    $s->execute([$id]);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

// ── Action: delete image ───────────────────────────────────────────────────

if ($action === 'delete_image' && $id) {
    $imageId = (int)($_GET['image_id'] ?? 0);
    if ($imageId) {
        $s = $pdo->prepare('SELECT filename FROM isopod_images WHERE id = ? AND isopod_id = ?');
        $s->execute([$imageId, $id]);
        $img = $s->fetch(PDO::FETCH_ASSOC);
        if ($img) {
            $file = __DIR__ . '/../images/' . $img['filename'];
            if (file_exists($file)) unlink($file);
            $pdo->prepare('DELETE FROM isopod_images WHERE id = ?')->execute([$imageId]);
        }
    }
    header("Location: edit-isopod.php?id=$id");
    exit;
}

// ── Action: upload images ──────────────────────────────────────────────────

if ($action === 'upload_image' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadDir = __DIR__ . '/../images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $files = $_FILES['images'] ?? [];
    $count = is_array($files['name']) ? count($files['name']) : 0;

    $s = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM isopod_images WHERE isopod_id = ?');
    $s->execute([$id]);
    $sortOrder = (int)$s->fetchColumn() + 1;

    $insert = $pdo->prepare('INSERT INTO isopod_images (isopod_id, filename, sort_order) VALUES (?, ?, ?)');
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (!in_array($files['type'][$i], $allowed)) continue;
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $filename = uniqid('isopod_', true) . '.' . $ext;
        if (move_uploaded_file($files['tmp_name'][$i], $uploadDir . $filename)) {
            $insert->execute([$id, $filename, $sortOrder++]);
        }
    }
    header("Location: edit-isopod.php?id=$id");
    exit;
}

// ── Action: save fields ────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'upload_image') {
    $fields = [
        'name'            => trim($_POST['name'] ?? ''),
        'scientific_name' => trim($_POST['scientific_name'] ?? ''),
        'description'     => trim($_POST['description'] ?? ''),
        'price'           => $_POST['price'] !== '' ? (float)$_POST['price'] : null,
        'available'       => isset($_POST['available']) ? 1 : 0,
        'obtained_from'   => trim($_POST['obtained_from'] ?? ''),
    ];

    if ($fields['name'] === '') {
        $error = 'Name is required.';
    } else {
        if ($id) {
            $set  = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $stmt = $pdo->prepare("UPDATE isopods SET $set WHERE id = ?");
            $stmt->execute([...array_values($fields), $id]);
        } else {
            $cols         = implode(', ', array_keys($fields));
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt         = $pdo->prepare("INSERT INTO isopods ($cols) VALUES ($placeholders)");
            $stmt->execute(array_values($fields));
            $id = (int)$pdo->lastInsertId();
        }
        header("Location: edit-isopod.php?id=$id");
        exit;
    }
}

// ── Load data ──────────────────────────────────────────────────────────────

$item = $id ? getIsopod($pdo, $id) : null;
if ($id && !$item) { header('Location: isopods.php'); exit; }
$images = $id ? getIsopodImages($pdo, $id) : [];
$v      = $item ?? array_fill_keys(['name','scientific_name','description','price','available','obtained_from'], '');
$title  = $id ? 'Edit Isopod' : 'Add Isopod';
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
  input[type=text], input[type=number], textarea {
    display: block; width: 100%; padding: .5rem .75rem;
    border: 1px solid #d1d5db; border-radius: 6px; font-size: .9rem; font-family: inherit;
  }
  textarea { resize: vertical; min-height: 80px; }
  .checkbox-row { display: flex; align-items: center; gap: .5rem; padding-top: 1.5rem; }
  .checkbox-row input { width: auto; }
  .footer { display: flex; gap: .75rem; margin-top: 1.5rem; }
  .btn { padding: .5rem 1.25rem; border-radius: 6px; font-size: .9rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; display: inline-block; }
  .btn-primary { background: #2563eb; color: #fff; }
  .btn-cancel  { background: #fff; color: #374151; border: 1px solid #d1d5db; }
  .btn-upload  { background: #059669; color: #fff; }
  .error { color: #dc2626; font-size: .875rem; margin-bottom: 1rem; }
  .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .image-item { position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; }
  .image-item img { width: 100%; height: 120px; object-fit: cover; display: block; }
  .image-item .delete-btn { position: absolute; top: 4px; right: 4px; background: rgba(220,38,38,.85); color: #fff; border: none; border-radius: 4px; padding: 2px 7px; font-size: .75rem; cursor: pointer; font-weight: 600; }
  .upload-zone { border: 2px dashed #d1d5db; border-radius: 8px; padding: 1.5rem; text-align: center; color: #6b7280; font-size: .875rem; }
  .upload-zone input[type=file] { margin-top: .75rem; }
  .empty-images { color: #9ca3af; font-size: .875rem; margin-bottom: 1rem; }
  .note { font-size: .8rem; color: #9ca3af; margin-top: .5rem; }
</style>
</head>
<body>
<?php require_once '_nav.php'; ?>

<div class="card">
  <h1><?= $title ?></h1>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post">
    <div class="grid">
      <div>
        <label>Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($v['name']) ?>" required>
      </div>
      <div>
        <label>Scientific Name</label>
        <input type="text" name="scientific_name" value="<?= htmlspecialchars($v['scientific_name']) ?>">
      </div>
      <div>
        <label>Price ($)</label>
        <input type="number" name="price" step="0.01"
               value="<?= $v['price'] !== null && $v['price'] !== '' ? htmlspecialchars((string)$v['price']) : '' ?>">
      </div>
      <div>
        <label>Obtained From</label>
        <input type="text" name="obtained_from" value="<?= htmlspecialchars($v['obtained_from']) ?>">
      </div>
      <div class="checkbox-row">
        <input type="checkbox" id="available" name="available" <?= $v['available'] ? 'checked' : '' ?>>
        <label for="available" style="margin:0">Available for sale</label>
      </div>
      <div class="full">
        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($v['description']) ?></textarea>
      </div>
    </div>
    <div class="footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="isopods.php" class="btn btn-cancel">Cancel</a>
    </div>
  </form>
</div>

<?php if ($id): ?>
<div class="card">
  <h2>Photos</h2>
  <?php if (empty($images)): ?>
    <p class="empty-images">No photos yet.</p>
  <?php else: ?>
    <div class="image-grid">
      <?php foreach ($images as $img): ?>
        <div class="image-item">
          <img src="../images/<?= htmlspecialchars($img['filename']) ?>" alt="" onerror="this.style.opacity='.3'">
          <a href="edit-isopod.php?id=<?= $id ?>&action=delete_image&image_id=<?= $img['id'] ?>"
             class="delete-btn" onclick="return confirm('Remove this photo?')">✕</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data" action="edit-isopod.php?id=<?= $id ?>&action=upload_image">
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
<div class="card"><p class="note">Save the isopod first, then you can add photos.</p></div>
<?php endif; ?>

</body>
</html>
