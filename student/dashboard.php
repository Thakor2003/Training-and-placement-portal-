1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	requireRole(ROLE_STUDENT);
4	
5	$userId = (int) $_SESSION['user_id'];
6	$db = getDB();
7	
8	$student = $db->prepare('SELECT s.*, b.name AS branch_name FROM students s LEFT JOIN branches b ON b.branch_id = s.branch_id WHERE s.user_id = ?');
9	$student->execute([$userId]);
10	$student = $student->fetch();
11	
12	$studentId = $student['student_id'];
13	
14	$skillCount = $db->prepare('SELECT COUNT(*) c FROM student_skills WHERE student_id = ?');
15	$skillCount->execute([$studentId]);
16	$skillCount = $skillCount->fetch()['c'];
17	
18	$resumeCount = $db->prepare('SELECT COUNT(*) c FROM resumes WHERE student_id = ?');
19	$resumeCount->execute([$studentId]);
20	$resumeCount = $resumeCount->fetch()['c'];
21	
22	$completion = calculateProfileCompletion($student, $skillCount, $resumeCount);
23	
24	$appStats = $db->prepare("SELECT status, COUNT(*) c FROM applications WHERE student_id = ? GROUP BY status");
25	$appStats->execute([$studentId]);
26	$statusCounts = ['applied' => 0, 'shortlisted' => 0, 'interview_scheduled' => 0, 'selected' => 0, 'rejected' => 0];
27	foreach ($appStats->fetchAll() as $row) { $statusCounts[$row['status']] = (int) $row['c']; }
28	$totalApplications = array_sum($statusCounts);
29	
30	$recentJobs = $db->query("SELECT j.*, c.company_name, c.logo_path FROM jobs j JOIN companies c ON c.company_id = j.company_id WHERE j.status='open' ORDER BY j.created_at DESC LIMIT 4")->fetchAll();
31	
32	$recentApps = $db->prepare("SELECT a.*, j.title, c.company_name FROM applications a JOIN jobs j ON j.job_id=a.job_id JOIN companies c ON c.company_id=j.company_id WHERE a.student_id=? ORDER BY a.applied_at DESC LIMIT 5");
33	$recentApps->execute([$studentId]);
34	$recentApps = $recentApps->fetchAll();
35	
36	$pageTitle = 'Student Dashboard';
37	$activePage = 'dashboard';
38	?>
39	<!DOCTYPE html>
40	<html lang="en">
41	<head>
42	<?php include __DIR__ . '/../includes/head.php'; ?>
43	<title>Dashboard | <?= e(SITE_NAME) ?></title>
44	</head>
45	<body>
46	<div class="app-shell">
47	  <?php include __DIR__ . '/../includes/sidebar.php'; ?>
48	
49	  <div class="main-content">
50	    <?php include __DIR__ . '/../includes/topbar.php'; ?>
51	
52	    <div class="content-area">
53	
54	      <!-- Welcome + profile completion -->
55	      <div class="card" style="margin-bottom:1.5rem; display:flex; flex-wrap:wrap; gap:1.5rem; align-items:center; justify-content:space-between;">
56	        <div>
57	          <div style="font-size:1.15rem; font-weight:700;">Welcome back, <?= e(explode(' ', currentUser()['full_name'])[0]) ?> 👋</div>
58	          <div class="text-muted-2" style="font-size:.88rem; margin-top:.2rem;">
59	            <?= e($student['branch_name'] ?? 'Branch not set') ?> · Semester <?= e((string)($student['semester'] ?? '-')) ?> · CGPA <?= e((string)($student['cgpa'] ?? '-')) ?>
60	          </div>
61	        </div>
62	        <div style="min-width:220px; flex:1; max-width:320px;">
63	          <div style="display:flex; justify-content:space-between; font-size:.8rem; margin-bottom:.4rem;">
64	            <span class="text-muted-2">Profile completion</span><strong><?= $completion ?>%</strong>
65	          </div>
66	          <div class="meter"><div class="meter-fill" style="width:<?= $completion ?>%;"></div></div>
67	          <?php if ($completion < 100): ?>
68	            <a href="<?= BASE_URL ?>/student/profile.php" style="font-size:.78rem; color:var(--amber); font-weight:600;">Complete your profile →</a>
69	          <?php endif; ?>
70	        </div>
71	      </div>
72	
73	      <!-- Stat cards -->
74	      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem;">
75	        <div class="stat-card">
76	          <div class="stat-value"><?= $totalApplications ?></div>
77	          <div class="stat-label">Applications sent</div>
78	        </div>
79	        <div class="stat-card">
80	          <div class="stat-value" style="color:var(--warn);"><?= $statusCounts['shortlisted'] + $statusCounts['interview_scheduled'] ?></div>
81	          <div class="stat-label">In progress</div>
82	        </div>
83	        <div class="stat-card">
84	          <div class="stat-value" style="color:var(--ok);"><?= $statusCounts['selected'] ?></div>
85	          <div class="stat-label">Offers received</div>
86	        </div>
87	        <div class="stat-card">
88	          <div class="stat-value"><?= $skillCount ?></div>
89	          <div class="stat-label">Skills listed</div>
90	        </div>
91	      </div>
92	
93	      <div style="display:grid; grid-template-columns:1.3fr 1fr; gap:1.5rem;" class="dash-grid">
94	        <!-- Application funnel chart -->
95	        <div class="card">
96	          <div style="font-weight:700; margin-bottom:1rem;">Application Funnel</div>
97	          <canvas id="funnelChart" height="180"></canvas>
98	        </div>
99	
100	        <!-- Recent applications -->
101	        <div class="card">
102	          <div style="font-weight:700; margin-bottom:1rem;">Recent Applications</div>
103	          <?php if (!$recentApps): ?>
104	            <div class="text-muted-2" style="font-size:.85rem;">No applications yet. <a href="<?= BASE_URL ?>/student/jobs.php" style="color:var(--amber);">Browse open jobs →</a></div>
105	          <?php else: foreach ($recentApps as $app): ?>
106	            <div style="display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid var(--line);">
107	              <div>
108	                <div style="font-weight:600; font-size:.88rem;"><?= e($app['title']) ?></div>
109	                <div class="text-muted-3" style="font-size:.78rem;"><?= e($app['company_name']) ?> · <?= e(timeAgo($app['applied_at'])) ?></div>
110	              </div>
111	              <span class="badge-status badge-<?= e(str_replace('interview_scheduled','shortlisted',$app['status'])) ?>"><?= e(str_replace('_',' ', $app['status'])) ?></span>
112	            </div>
113	          <?php endforeach; endif; ?>
114	        </div>
115	      </div>
116	
117	      <!-- Recommended jobs -->
118	      <div style="margin-top:1.5rem;">
119	        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
120	          <div style="font-weight:700;">Recommended for you</div>
121	          <a href="<?= BASE_URL ?>/student/jobs.php" style="font-size:.82rem; color:var(--amber); font-weight:600;">View all jobs →</a>
122	        </div>
123	        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem;">
124	          <?php foreach ($recentJobs as $job): ?>
125	            <div class="card">
126	              <div style="font-weight:700; font-size:.95rem; margin-bottom:.2rem;"><?= e($job['title']) ?></div>
127	              <div class="text-muted-2" style="font-size:.82rem; margin-bottom:.8rem;"><?= e($job['company_name']) ?> · <?= e($job['location']) ?></div>
128	              <div style="display:flex; justify-content:space-between; align-items:center;">
129	                <span class="badge-status badge-applied"><?= e(ucfirst(str_replace('_',' ',$job['job_type']))) ?></span>
130	                <a href="<?= BASE_URL ?>/student/job_detail.php?id=<?= $job['job_id'] ?>" class="btn btn-outline" style="padding:.4rem .9rem; font-size:.78rem;">View</a>
131	              </div>
132	            </div>
133	          <?php endforeach; ?>
134	        </div>
135	      </div>
136	
137	    </div>
138	  </div>
139	</div>
140	
141	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
142	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
143	<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
144	<script>
145	new Chart(document.getElementById('funnelChart'), {
146	  type: 'bar',
147	  data: {
148	    labels: ['Applied', 'Shortlisted', 'Interview', 'Selected', 'Rejected'],
149	    datasets: [{
150	      data: [
151	        <?= $statusCounts['applied'] ?>, <?= $statusCounts['shortlisted'] ?>,
152	        <?= $statusCounts['interview_scheduled'] ?>, <?= $statusCounts['selected'] ?>, <?= $statusCounts['rejected'] ?>
153	      ],
154	      backgroundColor: ['#7C8ADB','#E8A33D','#F4C978','#1E9E6B','#D94F4F'],
155	      borderRadius: 8,
156	    }]
157	  },
158	  options: {
159	    plugins: { legend: { display: false } },
160	    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
161	  }
162	});
163	</script>
164	<style>@media (max-width:900px){.dash-grid{grid-template-columns:1fr !important;}}</style>
165	</body>
166	</html>
