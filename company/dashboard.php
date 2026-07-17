1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	requireRole(ROLE_COMPANY);
4	$userId = (int) $_SESSION['user_id'];
5	$db = getDB();
6	$company = $db->prepare('SELECT * FROM companies WHERE user_id = ?');
7	$company->execute([$userId]);
8	$company = $company->fetch();
9	$jobCount = 0; $appCount = 0;
10	if ($company) {
11	    $jobCount = $db->prepare('SELECT COUNT(*) c FROM jobs WHERE company_id=?');
12	    $jobCount->execute([$company['company_id']]);
13	    $jobCount = $jobCount->fetch()['c'];
14	    $appCount = $db->prepare('SELECT COUNT(*) c FROM applications a JOIN jobs j ON j.job_id=a.job_id WHERE j.company_id=?');
15	    $appCount->execute([$company['company_id']]);
16	    $appCount = $appCount->fetch()['c'];
17	}
18	$pageTitle = 'Company Dashboard';
19	$activePage = 'dashboard';
20	?>
21	<!DOCTYPE html>
22	<html lang="en">
23	<head>
24	<?php include __DIR__ . '/../includes/head.php'; ?>
25	<title>Company Dashboard | <?= e(SITE_NAME) ?></title>
26	</head>
27	<body>
28	<div class="app-shell">
29	  <?php include __DIR__ . '/../includes/sidebar.php'; ?>
30	  <div class="main-content">
31	    <?php include __DIR__ . '/../includes/topbar.php'; ?>
32	    <div class="content-area">
33	      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem;">
34	        <div class="stat-card"><div class="stat-value"><?= $jobCount ?></div><div class="stat-label">Jobs posted</div></div>
35	        <div class="stat-card"><div class="stat-value"><?= $appCount ?></div><div class="stat-label">Applicants received</div></div>
36	        <div class="stat-card"><div class="stat-value"><?= $company['is_verified'] ? 'Verified' : 'Pending' ?></div><div class="stat-label">Account status</div></div>
37	      </div>
38	      <div class="card" style="margin-top:1.5rem;">
39	        <div style="font-weight:700; margin-bottom:.5rem;">Next up in this module</div>
40	        <div class="text-muted-2" style="font-size:.88rem;">Job posting form, applicant tracking board, interview scheduling and offer letter uploads build out on this shell next.</div>
41	      </div>
42	    </div>
43	  </div>
44	</div>
45	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
46	<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
47	</body>
48	</html>
