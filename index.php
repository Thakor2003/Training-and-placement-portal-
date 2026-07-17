1	<?php
2	require_once __DIR__ . '/config/config.php';
3	
4	if (isLoggedIn()) {
5	    redirect(dashboardForRole((int) $_SESSION['role_id']));
6	}
7	
8	$db = getDB();
9	$placedCount = $db->query('SELECT COUNT(*) c FROM placement_history')->fetch()['c'] ?? 0;
10	$companyCount = $db->query('SELECT COUNT(*) c FROM companies WHERE is_verified = 1')->fetch()['c'] ?? 0;
11	$openJobs = $db->query("SELECT COUNT(*) c FROM jobs WHERE status='open'")->fetch()['c'] ?? 0;
12	?>
13	<!DOCTYPE html>
14	<html lang="en">
15	<head>
16	<?php include __DIR__ . '/includes/head.php'; ?>
17	<title><?= e(SITE_NAME) ?> | <?= e(COLLEGE_NAME) ?></title>
18	</head>
19	<body>
20	
21	<!-- NAVBAR -->
22	<nav class="glass" style="position:sticky; top:0; z-index:100; padding:1rem 2rem; display:flex; align-items:center; justify-content:space-between;">
23	  <a href="<?= BASE_URL ?>/index.php" style="display:flex; align-items:center; gap:.5rem; font-family:var(--font-display); font-weight:700; font-size:1.1rem;">
24	    <i class="bi bi-mortarboard-fill" style="color:var(--amber);"></i> <?= e(SITE_NAME) ?>
25	  </a>
26	  <div class="d-none d-md-flex" style="gap:1.8rem; font-size:.9rem; font-weight:500;">
27	    <a href="#about">About</a>
28	    <a href="#stats">Placements</a>
29	    <a href="#recruiters">Recruiters</a>
30	    <a href="pages/gallery.php">Gallery</a>
31	    <a href="pages/contact.php">Contact</a>
32	  </div>
33	  <div style="display:flex; gap:.7rem; align-items:center;">
34	    <button class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode"></button>
35	    <a href="auth/login.php" class="btn btn-outline">Sign in</a>
36	    <a href="auth/register.php" class="btn btn-primary">Get started</a>
37	  </div>
38	</nav>
39	
40	<!-- HERO -->
41	<section class="gradient-hero" style="padding:6rem 2rem 5rem; position:relative; overflow:hidden;">
42	  <div style="max-width:960px; margin:0 auto; text-align:center;">
43	    <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(232,163,61,0.15); border:1px solid rgba(232,163,61,0.35); color:var(--amber-soft); padding:.4rem 1rem; border-radius:99px; font-size:.8rem; font-weight:600; margin-bottom:1.5rem;">
44	      <i class="bi bi-stars"></i> AI-powered career readiness, built in
45	    </div>
46	    <h1 class="display" style="font-size:clamp(2.2rem, 5vw, 3.4rem); color:#fff; line-height:1.15; margin-bottom:1.2rem;">
47	      Where <?= e(COLLEGE_NAME) ?> talent<br>meets its next opportunity.
48	    </h1>
49	    <p style="color:#AEB6D4; font-size:1.05rem; max-width:620px; margin:0 auto 2.2rem;">
50	      One secure portal for students, recruiters, and the placement office — job discovery,
51	      resume building, mock interviews, and placement analytics, all in one place.
52	    </p>
53	    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
54	      <a href="auth/register.php" class="btn btn-primary" style="padding:.85rem 1.8rem;">Create your account <i class="bi bi-arrow-right"></i></a>
55	      <a href="#stats" class="btn btn-outline" style="padding:.85rem 1.8rem; color:#fff; border-color:rgba(255,255,255,0.25);">See placement stats</a>
56	    </div>
57	  </div>
58	</section>
59	
60	<!-- STATS -->
61	<section id="stats" style="padding:3.5rem 2rem; background:var(--paper);">
62	  <div style="max-width:1000px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.2rem;">
63	    <div class="stat-card" style="text-align:center;">
64	      <div class="stat-value"><?= (int) $placedCount ?>+</div>
65	      <div class="stat-label">Students placed</div>
66	    </div>
67	    <div class="stat-card" style="text-align:center;">
68	      <div class="stat-value"><?= (int) $companyCount ?>+</div>
69	      <div class="stat-label">Verified recruiters</div>
70	    </div>
71	    <div class="stat-card" style="text-align:center;">
72	      <div class="stat-value"><?= (int) $openJobs ?></div>
73	      <div class="stat-label">Live openings</div>
74	    </div>
75	    <div class="stat-card" style="text-align:center;">
76	      <div class="stat-value">24/7</div>
77	      <div class="stat-label">AI career assistant</div>
78	    </div>
79	  </div>
80	</section>
81	
82	<!-- FEATURES -->
83	<section id="about" style="padding:4rem 2rem; background:var(--mist);">
84	  <div style="max-width:1100px; margin:0 auto;">
85	    <h2 class="display" style="text-align:center; font-size:1.9rem; margin-bottom:2.5rem;">Everything placement, in one workspace</h2>
86	    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1.3rem;">
87	      <?php
88	      $features = [
89	        ['bi-file-earmark-text', 'Resume Builder + ATS Score', 'Build a polished resume and get an instant AI-driven ATS score with suggestions.'],
90	        ['bi-briefcase', 'Job & Internship Search', 'Filter live openings by branch, CGPA and role — apply in a click, track every stage.'],
91	        ['bi-mic', 'AI Mock Interviews', 'Practice with role-specific AI-generated questions and get structured feedback.'],
92	        ['bi-graph-up', 'Placement Analytics', 'The placement office gets real-time dashboards on drives, offers and trends.'],
93	        ['bi-building', 'Recruiter Workspace', 'Companies post roles, manage applicants, and schedule interviews end to end.'],
94	        ['bi-shield-check', 'Verified & Secure', 'Role-based access, encrypted sessions and verified recruiter onboarding.'],
95	      ];
96	      foreach ($features as $f): ?>
97	        <div class="card reveal">
98	          <div style="width:44px; height:44px; border-radius:12px; background:rgba(232,163,61,0.15); display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
99	            <i class="bi <?= $f[0] ?>" style="color:var(--amber); font-size:1.3rem;"></i>
100	          </div>
101	          <div style="font-weight:700; margin-bottom:.4rem;"><?= e($f[1]) ?></div>
102	          <div class="text-muted-2" style="font-size:.88rem;"><?= e($f[2]) ?></div>
103	        </div>
104	      <?php endforeach; ?>
105	    </div>
106	  </div>
107	</section>
108	
109	<!-- CTA -->
110	<section id="recruiters" class="gradient-hero" style="padding:4rem 2rem; text-align:center;">
111	  <h2 class="display" style="color:#fff; font-size:1.7rem; margin-bottom:.8rem;">Hiring from <?= e(COLLEGE_NAME) ?>?</h2>
112	  <p style="color:#AEB6D4; margin-bottom:1.6rem;">Join our verified recruiter network and reach job-ready diploma & engineering talent.</p>
113	  <a href="auth/register.php" class="btn btn-primary" style="padding:.85rem 1.8rem;">Register as a recruiter</a>
114	</section>
115	
116	<?php include __DIR__ . '/includes/footer.php'; ?>
117	
118	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
119	<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
120	<style>.reveal{opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;}.reveal.is-visible{opacity:1;transform:none;}</style>
121	</body>
122	</html>
