1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	$sent = false;
5	
6	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
7	    verify_csrf();
8	    $email = clean($_POST['email'] ?? '');
9	
10	    if (validEmail($email)) {
11	        $db = getDB();
12	        $stmt = $db->prepare('SELECT user_id, full_name FROM users WHERE email = ?');
13	        $stmt->execute([$email]);
14	        $user = $stmt->fetch();
15	
16	        // Always show the same message whether or not the account exists (prevents email enumeration)
17	        if ($user) {
18	            $token = generateToken();
19	            $db->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')
20	               ->execute([$user['user_id'], $token]);
21	
22	            $resetLink = BASE_URL . '/auth/reset_password.php?token=' . $token;
23	            sendAppEmail(
24	                $email,
25	                'Reset your ' . SITE_NAME . ' password',
26	                "Hi {$user['full_name']},<br><br>Click below to reset your password (valid for 1 hour):<br>" .
27	                "<a href='{$resetLink}'>{$resetLink}</a><br><br>If you didn't request this, you can ignore this email."
28	            );
29	        }
30	        $sent = true;
31	    }
32	}
33	?>
34	<!DOCTYPE html>
35	<html lang="en">
36	<head>
37	<?php include __DIR__ . '/../includes/head.php'; ?>
38	<title>Forgot Password | <?= e(SITE_NAME) ?></title>
39	</head>
40	<body class="gradient-hero" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">
41	<div class="glass" style="width:100%; max-width:420px; border-radius:22px; padding:2.5rem;">
42	
43	  <?php if ($sent): ?>
44	    <div style="text-align:center;">
45	      <div style="font-size:2.2rem;">📩</div>
46	      <h2 style="color:#fff; margin:.8rem 0 .4rem;">Check your inbox</h2>
47	      <p style="color:#AEB6D4; font-size:.9rem;">If that email is registered, a password reset link is on its way.</p>
48	      <a href="login.php" class="btn btn-outline" style="margin-top:1.2rem; color:#fff;">Back to sign in</a>
49	    </div>
50	  <?php else: ?>
51	    <div style="text-align:center; margin-bottom:1.75rem;">
52	      <div class="display" style="font-size:1.4rem; color:#fff;">Reset your password</div>
53	      <div style="color:#AEB6D4; font-size:.9rem; margin-top:.3rem;">We'll email you a secure reset link.</div>
54	    </div>
55	    <form method="POST">
56	      <?= csrf_field() ?>
57	      <div style="margin-bottom:1.4rem;">
58	        <label class="form-label" style="color:#AEB6D4;">Email address</label>
59	        <input type="email" name="email" class="form-control" required autofocus>
60	      </div>
61	      <button type="submit" class="btn btn-primary" style="width:100%;">Send reset link</button>
62	    </form>
63	    <div style="text-align:center; margin-top:1.4rem; font-size:.87rem; color:#AEB6D4;">
64	      <a href="login.php" style="color:var(--amber-soft); font-weight:600;">Back to sign in</a>
65	    </div>
66	  <?php endif; ?>
67	</div>
68	</body>
69	</html>
