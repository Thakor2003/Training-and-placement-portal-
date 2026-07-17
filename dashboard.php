<?php
require_once __DIR__ . '/../config/config.php';
requireRole(ROLE_SUPER_ADMIN);
$db = getDB();
$totalUsers = $db->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
$totalStudents = $db->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
$totalCompanies = $db->query('SELECT COUNT(*) c FROM companies')->fetch()['c'];
$totalJobs = $db->query('SELECT COUNT(*) c FROM jobs')->fetch()['c'];
$pageTitle = 'Admin Dashboard';
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../includes/head.php'; ?>
<title>Admin Dashboard | <?= e(SITE_NAME) ?></title>
</head>
<body>
<div class="app-shell">
  <?php include __DIR__ . '/../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include __DIR__ . '/../includes/topbar.php'; ?>
    <div class="content-area">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem;">
        <div class="stat-card"><div class="stat-value"><?= $totalUsers ?></div><div class="stat-label">Total users</div></div>
        <div class="stat-card"><div class="stat-value"><?= $totalStudents ?></div><div class="stat-label">Students</div></div>
        <div class="stat-card"><div class="stat-value"><?= $totalCompanies ?></div><div class="stat-label">Companies</div></div>
        <div class="stat-card"><div class="stat-value"><?= $totalJobs ?></div><div class="stat-label">Jobs posted</div></div>
      </div>
      <div class="card" style="margin-top:1.5rem;">
        <div style="font-weight:700; margin-bottom:.5rem;">Next up in this module</div>
        <div class="text-muted-2" style="font-size:.88rem;">User Management, College Settings, Department/Branch Management, System Logs, Backup and CMS screens plug into this same shell.</div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
