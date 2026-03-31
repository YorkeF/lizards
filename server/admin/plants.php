<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../../config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$items = $pdo->query('SELECT * FROM plants ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Plants Admin</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #f4f4f5; padding: 2rem; }
  .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
  h1 { font-size: 1.5rem; }
  .actions a { text-decoration: none; padding: .5rem 1rem; border-radius: 6px; font-size: .875rem; font-weight: 500; }
  .btn-primary { background: #2563eb; color: #fff; }
  .btn-primary:hover { background: #1d4ed8; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
  th { background: #f9fafb; text-align: left; padding: .75rem 1rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
  td { padding: .75rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: .875rem; vertical-align: middle; }
  tr:last-child td { border-bottom: none; }
  .badge { display: inline-block; padding: .125rem .5rem; border-radius: 9999px; font-size: .75rem; font-weight: 500; }
  .badge-yes { background: #dcfce7; color: #166534; }
  .badge-no  { background: #fee2e2; color: #991b1b; }
  .row-actions a { text-decoration: none; font-size: .8rem; padding: .25rem .6rem; border-radius: 4px; }
  .btn-edit   { background: #eff6ff; color: #2563eb; }
  .btn-edit:hover { background: #dbeafe; }
  .btn-delete { background: #fff1f2; color: #dc2626; margin-left: .25rem; }
  .btn-delete:hover { background: #fee2e2; }
  .empty { padding: 2rem; text-align: center; color: #9ca3af; }
</style>
</head>
<body>
<?php require_once '_nav.php'; ?>
<div class="header">
  <h1>Plants</h1>
  <div class="actions">
    <a href="edit-plant.php" class="btn-primary">+ Add Plant</a>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Scientific Name</th>
      <th>Price</th>
      <th>Available</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($items)): ?>
      <tr><td colspan="5" class="empty">No plants yet. Add one above.</td></tr>
    <?php else: ?>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['name']) ?></td>
        <td><?= htmlspecialchars($item['scientific_name']) ?></td>
        <td><?= $item['price'] !== null ? '$' . number_format($item['price'], 2) : '—' ?></td>
        <td>
          <span class="badge <?= $item['available'] ? 'badge-yes' : 'badge-no' ?>">
            <?= $item['available'] ? 'Yes' : 'No' ?>
          </span>
        </td>
        <td class="row-actions">
          <a href="edit-plant.php?id=<?= $item['id'] ?>" class="btn-edit">Edit</a>
          <a href="delete-plant.php?id=<?= $item['id'] ?>" class="btn-delete"
             onclick="return confirm('Delete <?= htmlspecialchars(addslashes($item['name'])) ?>?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</body>
</html>
