1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	if (isLoggedIn()) {
5	    redirect(dashboardForRole((int) $_SESSION['role_id']));
6	}
7	
8	$error = null;
9	$emailOld = '';
10	
11	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
12	    verify_csrf();
13	
14	    $emailOld = clean($_POST['email'] ?? '');
15	    $password = $_POST['password'] ?? '';
16	    $rateKey  = 'login_' . $emailOld;
17	
18	    if (tooManyAttempts($rateKey)) {
19	        $error = 'Too many login attempts. Please wait a few minutes and try again.';
20	    } elseif (!validEmail($emailOld) || $password === '') {
21	        $error = 'Enter a valid email and password.';
22	    } else {
23	        $db = getDB();
24	        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
25	        $stmt->execute([$emailOld]);
26	        $user = $stmt->fetch();
27	
28	        if (!$user || !verifyPassword($password, $user['password_hash'])) {
29	            recordAttempt($rateKey);
30	            $error = 'Incorrect email or password.';
31	        } elseif (!$user['is_active']) {
32	            $error = 'Your account has been deactivated. Contact the placement office.';
33	        } elseif (!$user['is_email_verified']) {
34	            $error = 'Please verify your email before logging in. Check your inbox for the verification link.';
35	        } else {
36	            session_regenerate_id(true);
37	            $_SESSION['user_id'] = $user['user_id'];
38	            $_SESSION['role_id'] = $user['role_id'];
39	            $_SESSION['user']    = [
40	                'user_id'   => $user['user_id'],
41	                'full_name' => $user['full_name'],
42	                'email'     => $user['email'],
43	                'photo'     => $user['profile_photo'],
44	            ];
45	
46	            $db->prepare('INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)')
47	               ->execute([$user['user_id'], 'login', 'Successful login', $_SERVER['REMOTE_ADDR'] ?? '']);
48	
49	            redirect(dashboardForRole((int) $user['role_id']));
50	        }
51	    }
52	}
53	?>
54	<!DOCTYPE html>
55	<html lang="en">
56	<head>
57	<?php include __DIR__ . '/../includes/head.php'; ?>
58	<title>Sign In | <?= e(SITE_NAME) ?></title>
59	</head>
60	<body class="gradient-hero" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">
61	
62	<div class="glass" style="width:100%; max-width:420px; border-radius:22px; padding:2.5rem;">
63	  <div style="text-align:center; margin-bottom:1.75rem;">
64	    <div class="display" style="font-size:1.4rem; color:#fff;">Welcome back</div>
65	    <div style="color:#AEB6D4; font-size:.9rem; margin-top:.3rem;"><?= e(COLLEGE_NAME) ?> · Training & Placement Portal</div>
66	  </div>
67	
68	  <?php if ($msg = flash('success')): ?>
69	    <div style="background:rgba(30,158,107,0.15); border:1px solid rgba(30,158,107,0.4); color:#9EE8C8; padding:.9rem 1rem; border-radius:10px; margin-bottom:1.2rem; font-size:.85rem;"><?= e($msg) ?></div>
70	  <?php endif; ?>
71	  <?php if ($error): ?>
72	    <div style="background:rgba(217,79,79,0.15); border:1px solid rgba(217,79,79,0.4); color:#F3B4B4; padding:.9rem 1rem; border-radius:10px; margin-bottom:1.2rem; font-size:.85rem;"><?= e($error) ?></div>
73	  <?php endif; ?>
74	
75	  <form method="POST" novalidate>
76	    <?= csrf_field() ?>
77	    <div style="margin-bottom:1rem;">
78	      <label class="form-label" style="color:#AEB6D4;">Email address</label>
79	      <input type="email" name="email" class="form-control" value="<?= e($emailOld) ?>" required autofocus>
80	    </div>
81	    <div style="margin-bottom:.6rem;">
82	      <label class="form-label" style="color:#AEB6D4;">Password</label>
83	      <input type="password" name="password" class="form-control" required>
84	    </div>
85	    <div style="text-align:right; margin-bottom:1.4rem;">
86	      <a href="forgot_password.php" style="font-size:.82rem; color:var(--amber-soft);">Forgot password?</a>
87	    </div>
88	    <button type="submit" class="btn btn-primary" style="width:100%;">Sign in</button>
89	  </form>
90	
91	  <div style="text-align:center; margin-top:1.4rem; font-size:.87rem; color:#AEB6D4;">
92	    New here? <a href="register.php" style="color:var(--amber-soft); font-weight:600;">Create an account</a>
93	  </div>
94	
95	  <div style="margin-top:1.5rem; padding-top:1.2rem; border-top:1px solid rgba(255,255,255,0.1); font-size:.75rem; color:var(--text-3); text-align:center;">
96	    Demo logins (password: <code>Passw0rd!</code>)<br>
97	    admin@tpp.local · tpo@tpp.local · student@tpp.local · company@tpp.local
98	  </div>
99	</div>
100	</body>
101	</html>
