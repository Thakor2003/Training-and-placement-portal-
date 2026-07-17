1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	$token = $_GET['token'] ?? '';
5	$status = 'invalid';
6	
7	if ($token) {
8	    $db = getDB();
9	    $stmt = $db->prepare('SELECT * FROM email_verifications WHERE token = ? AND expires_at > NOW()');
10	    $stmt->execute([$token]);
11	    $row = $stmt->fetch();
12	
13	    if ($row) {
14	        $db->prepare('UPDATE users SET is_email_verified = 1 WHERE user_id = ?')->execute([$row['user_id']]);
15	        $db->prepare('DELETE FROM email_verifications WHERE user_id = ?')->execute([$row['user_id']]);
16	        $status = 'success';
17	    }
18	}
19	?>
20	<!DOCTYPE html>
21	<html lang="en">
22	<head>
23	<?php include __DIR__ . '/../includes/head.php'; ?>
24	<title>Email Verification | <?= e(SITE_NAME) ?></title>
25	</head>
26	<body class="gradient-hero" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">
27	  <div class="glass" style="max-width:440px; text-align:center; border-radius:22px; padding:2.5rem;">
28	    <?php if ($status === 'success'): ?>
29	      <div style="font-size:2.2rem;">✅</div>
30	      <h2 style="color:#fff; margin:.8rem 0 .4rem;">Email verified</h2>
31	      <p style="color:#AEB6D4; font-size:.9rem;">Your account is active. You can now sign in.</p>
32	    <?php else: ?>
33	      <div style="font-size:2.2rem;">⚠️</div>
34	      <h2 style="color:#fff; margin:.8rem 0 .4rem;">Link invalid or expired</h2>
35	      <p style="color:#AEB6D4; font-size:.9rem;">Please request a new verification email from the login page.</p>
36	    <?php endif; ?>
37	    <a href="login.php" class="btn btn-primary" style="margin-top:1.2rem;">Go to sign in</a>
38	  </div>
39	</body>
40	</html>
