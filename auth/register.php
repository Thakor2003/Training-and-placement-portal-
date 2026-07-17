1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	if (isLoggedIn()) {
5	    redirect(dashboardForRole((int) $_SESSION['role_id']));
6	}
7	
8	$errors = [];
9	$old = ['full_name' => '', 'email' => '', 'role_id' => ROLE_STUDENT];
10	
11	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
12	    verify_csrf();
13	
14	    $old['full_name'] = clean($_POST['full_name'] ?? '');
15	    $old['email']     = clean($_POST['email'] ?? '');
16	    $old['role_id']   = (int) ($_POST['role_id'] ?? ROLE_STUDENT);
17	    $password         = $_POST['password'] ?? '';
18	    $confirm          = $_POST['confirm_password'] ?? '';
19	
20	    if ($old['full_name'] === '') $errors[] = 'Full name is required.';
21	    if (!validEmail($old['email'])) $errors[] = 'Enter a valid email address.';
22	    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
23	    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
24	    if (!in_array($old['role_id'], [ROLE_STUDENT, ROLE_COMPANY], true)) {
25	        $errors[] = 'Invalid role selected.'; // Admin/TPO accounts are provisioned internally
26	    }
27	
28	    if (!$errors) {
29	        $db = getDB();
30	        $stmt = $db->prepare('SELECT user_id FROM users WHERE email = ?');
31	        $stmt->execute([$old['email']]);
32	        if ($stmt->fetch()) {
33	            $errors[] = 'An account with this email already exists.';
34	        }
35	    }
36	
37	    if (!$errors) {
38	        $db = getDB();
39	        $db->beginTransaction();
40	        try {
41	            $stmt = $db->prepare(
42	                'INSERT INTO users (role_id, full_name, email, password_hash) VALUES (?, ?, ?, ?)'
43	            );
44	            $stmt->execute([$old['role_id'], $old['full_name'], $old['email'], hashPassword($password)]);
45	            $userId = (int) $db->lastInsertId();
46	
47	            if ($old['role_id'] === ROLE_STUDENT) {
48	                $db->prepare('INSERT INTO students (user_id) VALUES (?)')->execute([$userId]);
49	            } elseif ($old['role_id'] === ROLE_COMPANY) {
50	                $db->prepare('INSERT INTO companies (user_id, company_name) VALUES (?, ?)')
51	                   ->execute([$userId, $old['full_name']]);
52	            }
53	
54	            // Email verification token
55	            $token = generateToken();
56	            $db->prepare('INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))')
57	               ->execute([$userId, $token]);
58	
59	            $db->commit();
60	
61	            $verifyLink = BASE_URL . '/auth/verify_email.php?token=' . $token;
62	            sendAppEmail(
63	                $old['email'],
64	                'Verify your ' . SITE_NAME . ' account',
65	                "Hi {$old['full_name']},<br><br>Welcome to " . SITE_NAME . ". Please verify your email:<br>" .
66	                "<a href='{$verifyLink}'>{$verifyLink}</a><br><br>This link expires in 24 hours."
67	            );
68	
69	            flash('success', 'Account created! Please check your email to verify your account before logging in.');
70	            redirect('auth/login.php');
71	        } catch (Exception $e) {
72	            $db->rollBack();
73	            error_log($e->getMessage());
74	            $errors[] = 'Something went wrong while creating your account. Please try again.';
75	        }
76	    }
77	}
78	?>
79	<!DOCTYPE html>
80	<html lang="en">
81	<head>
82	<?php include __DIR__ . '/../includes/head.php'; ?>
83	<title>Create Account | <?= e(SITE_NAME) ?></title>
84	</head>
85	<body class="gradient-hero" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">
86	
87	<div class="glass" style="width:100%; max-width:460px; border-radius:22px; padding:2.5rem;">
88	  <div style="text-align:center; margin-bottom:1.75rem;">
89	    <div class="display" style="font-size:1.4rem; color:#fff;">Create your account</div>
90	    <div style="color:#AEB6D4; font-size:.9rem; margin-top:.3rem;"><?= e(COLLEGE_NAME) ?> · Training & Placement Portal</div>
91	  </div>
92	
93	  <?php if ($errors): ?>
94	    <div style="background:rgba(217,79,79,0.15); border:1px solid rgba(217,79,79,0.4); color:#F3B4B4; padding:.9rem 1rem; border-radius:10px; margin-bottom:1.2rem; font-size:.85rem;">
95	      <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
96	    </div>
97	  <?php endif; ?>
98	
99	  <form method="POST" novalidate>
100	    <?= csrf_field() ?>
101	
102	    <div style="display:flex; gap:.6rem; margin-bottom:1.1rem;">
103	      <label style="flex:1; cursor:pointer;">
104	        <input type="radio" name="role_id" value="<?= ROLE_STUDENT ?>" <?= $old['role_id'] == ROLE_STUDENT ? 'checked' : '' ?> style="display:none;" class="role-radio">
105	        <div class="role-box" data-active="<?= $old['role_id'] == ROLE_STUDENT ? '1' : '0' ?>">🎓 Student</div>
106	      </label>
107	      <label style="flex:1; cursor:pointer;">
108	        <input type="radio" name="role_id" value="<?= ROLE_COMPANY ?>" <?= $old['role_id'] == ROLE_COMPANY ? 'checked' : '' ?> style="display:none;" class="role-radio">
109	        <div class="role-box" data-active="<?= $old['role_id'] == ROLE_COMPANY ? '1' : '0' ?>">🏢 Recruiter</div>
110	      </label>
111	    </div>
112	
113	    <div style="margin-bottom:1rem;">
114	      <label class="form-label" style="color:#AEB6D4;">Full name</label>
115	      <input type="text" name="full_name" class="form-control" value="<?= e($old['full_name']) ?>" required>
116	    </div>
117	    <div style="margin-bottom:1rem;">
118	      <label class="form-label" style="color:#AEB6D4;">Email address</label>
119	      <input type="email" name="email" class="form-control" value="<?= e($old['email']) ?>" required>
120	    </div>
121	    <div style="margin-bottom:1rem;">
122	      <label class="form-label" style="color:#AEB6D4;">Password</label>
123	      <input type="password" name="password" class="form-control" minlength="8" required>
124	    </div>
125	    <div style="margin-bottom:1.4rem;">
126	      <label class="form-label" style="color:#AEB6D4;">Confirm password</label>
127	      <input type="password" name="confirm_password" class="form-control" minlength="8" required>
128	    </div>
129	
130	    <button type="submit" class="btn btn-primary" style="width:100%;">Create account</button>
131	  </form>
132	
133	  <div style="text-align:center; margin-top:1.4rem; font-size:.87rem; color:#AEB6D4;">
134	    Already have an account? <a href="login.php" style="color:var(--amber-soft); font-weight:600;">Sign in</a>
135	  </div>
136	</div>
137	
138	<style>
139	.role-box {
140	  text-align:center; padding:.7rem; border-radius:10px;
141	  border:1px solid rgba(255,255,255,0.15); color:#AEB6D4; font-size:.85rem; font-weight:600;
142	  transition: all .15s ease;
143	}
144	.role-box[data-active="1"] { border-color: var(--amber); color:#fff; background: rgba(232,163,61,0.15); }
145	</style>
146	<script>
147	document.querySelectorAll('.role-radio').forEach(r => r.addEventListener('change', () => {
148	  document.querySelectorAll('.role-box').forEach(b => b.dataset.active = '0');
149	  r.nextElementSibling.dataset.active = '1';
150	}));
151	</script>
152	</body>
153	</html>
