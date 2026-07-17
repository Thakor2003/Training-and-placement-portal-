1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	requireRole(ROLE_SUPER_ADMIN);
4	$db = getDB();
5	$totalUsers = $db->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
6	$totalStudents = $db->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
7	$totalCompanies = $db->query('SELECT COUNT(*) c FROM companies')->fetch()['c'];
8	$totalJobs = $db->query('SELECT COUNT(*) c FROM jobs')->fetch()['c'];
9	$pageTitle = 'Admin Dashboard';
10	$activePage = 'dashboard';
11	?>
12	<!DOCTYPE html>
13	<html lang="en">
14	<head>
15	<?php include __DIR__ . '/../includes/head.php'; ?>
16	<title>Admin Dashboard | <?= e(SITE_NAME) ?></title>
17	</head>
18	<body>
19	<div class="app-shell">
20	  <?php include __DIR__ . '/../includes/sidebar.php'; ?>
21	  <div class="main-content">
22	    <?php include __DIR__ . '/../includes/topbar.php'; ?>
23	    <div class="content-area">
24	      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem;">
25	        <div class="stat-card"><div class="stat-value"><?= $totalUsers ?></div><div class="stat-label">Total users</div></div>
26	        <div class="stat-card"><div class="stat-value"><?= $totalStudents ?></div><div class="stat-label">Students</div></div>
27	        <div class="stat-card"><div class="stat-value"><?= $totalCompanies ?></div><div class="stat-label">Companies</div></div>
28	        <div class="stat-card"><div class="stat-value"><?= $totalJobs ?></div><div class="stat-label">Jobs posted</div></div>
29	      </div>
30	      <div class="card" style="margin-top:1.5rem;">
31	        <div style="font-weight:700; margin-bottom:.5rem;">Next up in this module</div>
32	        <div class="text-muted-2" style="font-size:.88rem;">User Management, College Settings, Department/Branch Management, System Logs, Backup and CMS screens plug into this same shell.</div>
33	      </div>
34	    </div>
35	  </div>
36	</div>
37	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
38	<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
39	</body>
40	</html>
