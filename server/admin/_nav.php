<?php
// Shared admin navigation bar — include at the top of every admin page.
$current = basename($_SERVER['PHP_SELF']);
function navActive(string $file, string $current): string {
    return $file === $current ? 'nav-active' : '';
}
?>
<nav class="admin-nav">
  <span class="admin-nav-brand">Lichen Leachies Admin</span>
  <div class="admin-nav-links">
    <a href="index.php"    class="<?= navActive('index.php',    $current) ?>">Lizards</a>
    <a href="isopods.php"  class="<?= navActive('isopods.php',  $current) ?>">Isopods</a>
    <a href="plants.php"   class="<?= navActive('plants.php',   $current) ?>">Plants</a>
  </div>
  <a href="logout.php" class="admin-nav-logout">Log out</a>
</nav>
<style>
  .admin-nav {
    display: flex; align-items: center; gap: 1.5rem;
    background: #1e293b; color: #f1f5f9;
    padding: .75rem 2rem; margin: -2rem -2rem 2rem;
    font-size: .875rem;
  }
  .admin-nav-brand { font-weight: 700; font-size: 1rem; margin-right: auto; }
  .admin-nav-links { display: flex; gap: 1rem; }
  .admin-nav-links a { color: #94a3b8; text-decoration: none; font-weight: 500; padding: .25rem .5rem; border-radius: 4px; }
  .admin-nav-links a:hover { color: #f1f5f9; background: #334155; }
  .admin-nav-links a.nav-active { color: #f1f5f9; background: #2563eb; }
  .admin-nav-logout { color: #94a3b8; text-decoration: none; font-size: .8rem; }
  .admin-nav-logout:hover { color: #f1f5f9; }
</style>
