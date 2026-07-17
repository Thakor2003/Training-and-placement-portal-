1	<?php
2	/**
3	 * Security helpers: CSRF protection, input sanitization, XSS escaping,
4	 * role guards and session helpers.
5	 */
6	
7	// ---------- CSRF ----------
8	
9	function csrf_token(): string
10	{
11	    if (empty($_SESSION['csrf_token'])) {
12	        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
13	    }
14	    return $_SESSION['csrf_token'];
15	}
16	
17	function csrf_field(): string
18	{
19	    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
20	}
21	
22	function verify_csrf(): void
23	{
24	    $token = $_POST['csrf_token'] ?? '';
25	    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
26	        http_response_code(403);
27	        die('Invalid or expired form submission (CSRF check failed). Please go back and try again.');
28	    }
29	}
30	
31	// ---------- Output escaping (XSS protection) ----------
32	
33	function e(?string $value): string
34	{
35	    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
36	}
37	
38	// ---------- Input sanitization ----------
39	
40	function clean(string $value): string
41	{
42	    return trim(strip_tags($value));
43	}
44	
45	function validEmail(string $email): bool
46	{
47	    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
48	}
49	
50	// ---------- Auth / session guards ----------
51	
52	function isLoggedIn(): bool
53	{
54	    return isset($_SESSION['user_id']);
55	}
56	
57	function currentUser(): ?array
58	{
59	    return $_SESSION['user'] ?? null;
60	}
61	
62	function requireLogin(): void
63	{
64	    if (!isLoggedIn()) {
65	        header('Location: ' . BASE_URL . '/auth/login.php');
66	        exit;
67	    }
68	}
69	
70	function requireRole(int ...$roleIds): void
71	{
72	    requireLogin();
73	    if (!in_array((int) $_SESSION['role_id'], $roleIds, true)) {
74	        http_response_code(403);
75	        die('You do not have permission to access this page.');
76	    }
77	}
78	
79	// ---------- Password ----------
80	
81	function hashPassword(string $plain): string
82	{
83	    return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
84	}
85	
86	function verifyPassword(string $plain, string $hash): bool
87	{
88	    return password_verify($plain, $hash);
89	}
90	
91	// Simple rate limiting for login attempts (per session, demo-grade)
92	function tooManyAttempts(string $key, int $max = 5, int $windowSeconds = 300): bool
93	{
94	    $now = time();
95	    $_SESSION['attempts'][$key] = array_filter(
96	        $_SESSION['attempts'][$key] ?? [],
97	        fn($t) => $t > $now - $windowSeconds
98	    );
99	    return count($_SESSION['attempts'][$key]) >= $max;
100	}
101	
102	function recordAttempt(string $key): void
103	{
104	    $_SESSION['attempts'][$key][] = time();
105	}
