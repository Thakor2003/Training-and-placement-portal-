1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	$token = $_GET['token'] ?? $_POST['token'] ?? '';
5	$errors = [];
6	$success = false;
7	$validToken = false;
8	
9	if ($token) {
10	    $db = getDB();
11	    $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()');
12	    $stmt->execute([$token]);
13	    $reset = $stmt->fetch();
14	    $validToken = (bool) $reset;
15	} else {
16	    $reset = null;
17	}
18	
19	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
20	    verify_csrf();
21	    $password = $_POST['password'] ?? '';
22	    $confirm  = $_POST['confirm_password'] ?? '';
23	
24	    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
25	    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
26	
27	    if (!$errors) {
28	        $db->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?')
29	           ->execute([hashPassword($password), $reset['user_id']]);
30	        $db->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$reset['id']]);
31	        $success = true;
32	    }
33	}
34	?>
35	<!DOCTYPE html>
36	<html lang="en">
37	<head>
38	<?php include __DIR__ . '/../includes/head.php'; ?>
39	<title>Reset Password | <?= e(SITE_NAME) ?></title>
40	</head>
41	<body class="gradient-hero" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">
42	<div class="glass" style="width:100%; max-width:420px; border-radius:22px; padding:2.5rem;">
43	
44	  <?php if (!$validToken && !$success): ?>
45	    <div style="text-align:center;">
46	      <div style="font-size:2.2rem;">⚠️</div>
47	      <h2 style="color:#fff; margin:.8rem 0 .4rem;">Link invalid or expired</h2>
48	      <p style="color:#AEB6D4; font-size:.9rem;">Please request a new password reset link.</p>
49	      <a href="forgot_password.php" class="btn btn-primary" style="margin-top:1.2rem;">Request new link</a>
50	    </div>
51	  <?php elseif ($success): ?>
52	    <div style="text-align:center;">
53	      <div style="font-size:2.2rem;">✅</div>
54	      <h2 style="color:#fff; margin:.8rem 0 .4rem;">Password updated</h2>
55	      <p style="color:#AEB6D4; font-size:.9rem;">You can now sign in with your new password.</p>
56	      <a href="login.php" class="btn btn-primary" style="margin-top:1.2rem;">Go to sign in</a>
57	    </div>
58	  <?php else: ?>
59	    <div style="text-align:center; margin-bottom:1.75rem;">
60	      <div class="display" style="font-size:1.4rem; color:#fff;">Set a new password</div>
61	    </div>
62	    <?php if ($errors): ?>
63	      <div style="background:rgba(217,79,79,0.15); border:1px solid rgba(217,79,79,0.4); color:#F3B4B4; padding:.9rem 1rem; border-radius:10px; margin-bottom:1.2rem; font-size:.85rem;">
64	        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
65	      </div>
66	    <?php endif; ?>
67	    <form method="POST">
68	      <?= csrf_field() ?>
69	      <input type="hidden" name="token" value="<?= e($token) ?>">
70	      <div style="margin-bottom:1rem;">
71	        <label class="form-label" style="color:#AEB6D4;">New password</label>
72	        <input type="password" name="password" class="form-control" minlength="8" required autofocus>
73	      </div>
74	      <div style="margin-bottom:1.4rem;">
75	        <label class="form-label" style="color:#AEB6D4;">Confirm new password</label>
76	        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
77	      </div>
78	      <button type="submit" class="btn btn-primary" style="width:100%;">Update password</button>
79	    </form>
80	  <?php endif; ?>
81	</div>
82	</body>
83	</html>
