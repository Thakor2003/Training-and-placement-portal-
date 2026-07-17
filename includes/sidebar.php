1	<?php
2	/**
3	 * Renders the sidebar based on the logged-in user's role.
4	 * Expects $activePage to be set by the including page (e.g. 'dashboard').
5	 */
6	$activePage = $activePage ?? '';
7	$roleId = (int) ($_SESSION['role_id'] ?? 0);
8	
9	function navLink(string $href, string $icon, string $label, string $key, string $active): void {
10	    $isActive = $active === $key ? 'active' : '';
11	    echo '<a class="nav-link ' . $isActive . '" href="' . BASE_URL . '/' . e($href) . '"><i class="bi ' . e($icon) . '"></i> ' . e($label) . '</a>';
12	}
13	?>
14	<aside class="sidebar">
15	  <div class="brand">
16	    <i class="bi bi-mortarboard-fill" style="color:var(--amber);"></i>
17	    <span><?= e(SITE_NAME) ?></span>
18	  </div>
19	
20	  <?php if ($roleId === ROLE_STUDENT): ?>
21	    <?php navLink('student/dashboard.php', 'bi-speedometer2', 'Dashboard', 'dashboard', $activePage) ?>
22	    <?php navLink('student/profile.php', 'bi-person-badge', 'My Profile', 'profile', $activePage) ?>
23	    <div class="nav-section">Career</div>
24	    <?php navLink('student/resume_builder.php', 'bi-file-earmark-text', 'Resume Builder', 'resume', $activePage) ?>
25	    <?php navLink('student/jobs.php', 'bi-briefcase', 'Job Search', 'jobs', $activePage) ?>
26	    <?php navLink('student/internships.php', 'bi-laptop', 'Internships', 'internships', $activePage) ?>
27	    <?php navLink('student/applications.php', 'bi-send-check', 'My Applications', 'applications', $activePage) ?>
28	    <?php navLink('student/saved_jobs.php', 'bi-bookmark-heart', 'Saved Jobs', 'saved', $activePage) ?>
29	    <?php navLink('student/placement_history.php', 'bi-trophy', 'Placement History', 'history', $activePage) ?>
30	    <div class="nav-section">AI Tools</div>
31	    <?php navLink('student/ai_resume_feedback.php', 'bi-stars', 'AI Resume Feedback', 'ai_resume', $activePage) ?>
32	    <?php navLink('student/mock_interview.php', 'bi-mic', 'Mock Interview', 'mock', $activePage) ?>
33	    <?php navLink('student/ai_chatbot.php', 'bi-chat-dots', 'AI Career Chatbot', 'chatbot', $activePage) ?>
34	    <div class="nav-section">Other</div>
35	    <?php navLink('student/calendar.php', 'bi-calendar3', 'Calendar', 'calendar', $activePage) ?>
36	    <?php navLink('student/notifications.php', 'bi-bell', 'Notifications', 'notifications', $activePage) ?>
37	
38	  <?php elseif ($roleId === ROLE_COMPANY): ?>
39	    <?php navLink('company/dashboard.php', 'bi-speedometer2', 'Dashboard', 'dashboard', $activePage) ?>
40	    <?php navLink('company/profile.php', 'bi-building', 'Company Profile', 'profile', $activePage) ?>
41	    <?php navLink('company/hr_management.php', 'bi-people', 'HR Management', 'hr', $activePage) ?>
42	    <div class="nav-section">Hiring</div>
43	    <?php navLink('company/post_job.php', 'bi-plus-circle', 'Post Job / Internship', 'post_job', $activePage) ?>
44	    <?php navLink('company/applicants.php', 'bi-person-lines-fill', 'View Applicants', 'applicants', $activePage) ?>
45	    <?php navLink('company/interviews.php', 'bi-calendar-event', 'Schedule Interviews', 'interviews', $activePage) ?>
46	    <?php navLink('company/offer_letters.php', 'bi-file-earmark-arrow-up', 'Offer Letters', 'offers', $activePage) ?>
47	    <?php navLink('company/statistics.php', 'bi-bar-chart', 'Placement Statistics', 'stats', $activePage) ?>
48	
49	  <?php elseif ($roleId === ROLE_TPO): ?>
50	    <?php navLink('tpo/dashboard.php', 'bi-speedometer2', 'Dashboard', 'dashboard', $activePage) ?>
51	    <div class="nav-section">Management</div>
52	    <?php navLink('tpo/students.php', 'bi-people', 'Student Management', 'students', $activePage) ?>
53	    <?php navLink('tpo/companies.php', 'bi-building', 'Company Management', 'companies', $activePage) ?>
54	    <?php navLink('tpo/jobs.php', 'bi-briefcase', 'Job Management', 'jobs', $activePage) ?>
55	    <?php navLink('tpo/drives.php', 'bi-flag', 'Placement Drives', 'drives', $activePage) ?>
56	    <?php navLink('tpo/interviews.php', 'bi-calendar-event', 'Interview Schedule', 'interviews', $activePage) ?>
57	    <?php navLink('tpo/eligibility.php', 'bi-check2-square', 'Eligibility Criteria', 'eligibility', $activePage) ?>
58	    <?php navLink('tpo/notices.php', 'bi-megaphone', 'Notice Management', 'notices', $activePage) ?>
59	    <?php navLink('tpo/resume_verification.php', 'bi-patch-check', 'Resume Verification', 'verify', $activePage) ?>
60	    <div class="nav-section">Insights</div>
61	    <?php navLink('tpo/reports.php', 'bi-file-earmark-bar-graph', 'Reports', 'reports', $activePage) ?>
62	    <?php navLink('tpo/analytics.php', 'bi-graph-up', 'Analytics', 'analytics', $activePage) ?>
63	
64	  <?php elseif ($roleId === ROLE_SUPER_ADMIN): ?>
65	    <?php navLink('admin/dashboard.php', 'bi-speedometer2', 'Dashboard', 'dashboard', $activePage) ?>
66	    <div class="nav-section">Administration</div>
67	    <?php navLink('admin/users.php', 'bi-people', 'User Management', 'users', $activePage) ?>
68	    <?php navLink('admin/college_settings.php', 'bi-gear', 'College Settings', 'settings', $activePage) ?>
69	    <?php navLink('admin/departments.php', 'bi-diagram-3', 'Departments & Branches', 'departments', $activePage) ?>
70	    <?php navLink('admin/academic_years.php', 'bi-calendar-range', 'Academic Years', 'years', $activePage) ?>
71	    <?php navLink('admin/cms.php', 'bi-window-stack', 'Website CMS', 'cms', $activePage) ?>
72	    <div class="nav-section">System</div>
73	    <?php navLink('admin/notifications.php', 'bi-bell', 'Notification Management', 'notifications', $activePage) ?>
74	    <?php navLink('admin/logs.php', 'bi-clock-history', 'System Logs', 'logs', $activePage) ?>
75	    <?php navLink('admin/backup.php', 'bi-cloud-arrow-down', 'Database Backup', 'backup', $activePage) ?>
76	    <?php navLink('admin/security.php', 'bi-shield-lock', 'Security Settings', 'security', $activePage) ?>
77	  <?php endif; ?>
78	
79	  <div class="nav-section">Account</div>
80	  <a class="nav-link" href="<?= BASE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
81	</aside>
