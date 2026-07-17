1	<?php
2	/**
3	 * Reusable helper functions used across modules.
4	 */
5	
6	function redirect(string $path): void
7	{
8	    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
9	    exit;
10	}
11	
12	function flash(string $key, ?string $message = null)
13	{
14	    if ($message !== null) {
15	        $_SESSION['flash'][$key] = $message;
16	        return null;
17	    }
18	    $msg = $_SESSION['flash'][$key] ?? null;
19	    unset($_SESSION['flash'][$key]);
20	    return $msg;
21	}
22	
23	/** Route a logged-in user to their role's dashboard */
24	function dashboardForRole(int $roleId): string
25	{
26	    return match ($roleId) {
27	        ROLE_SUPER_ADMIN => 'admin/dashboard.php',
28	        ROLE_TPO         => 'tpo/dashboard.php',
29	        ROLE_STUDENT     => 'student/dashboard.php',
30	        ROLE_COMPANY     => 'company/dashboard.php',
31	        default          => 'index.php',
32	    };
33	}
34	
35	function roleName(int $roleId): string
36	{
37	    return match ($roleId) {
38	        ROLE_SUPER_ADMIN => 'Super Admin',
39	        ROLE_TPO         => 'Training & Placement Officer',
40	        ROLE_STUDENT     => 'Student',
41	        ROLE_COMPANY     => 'Company / Recruiter',
42	        default          => 'User',
43	    };
44	}
45	
46	/** Calculate a student's profile completion percentage */
47	function calculateProfileCompletion(array $student, int $skillCount, int $resumeCount): int
48	{
49	    $fields = [
50	        $student['cgpa'], $student['date_of_birth'], $student['gender'],
51	        $student['address'], $student['city'], $student['linkedin_url'],
52	        $student['github_url'],
53	    ];
54	    $filled = count(array_filter($fields, fn($v) => !empty($v)));
55	    $total = count($fields) + 2; // +skills +resume
56	
57	    if ($skillCount > 0) $filled++;
58	    if ($resumeCount > 0) $filled++;
59	
60	    return (int) round(($filled / $total) * 100);
61	}
62	
63	function timeAgo(string $datetime): string
64	{
65	    $diff = time() - strtotime($datetime);
66	    if ($diff < 60) return 'just now';
67	    if ($diff < 3600) return floor($diff / 60) . 'm ago';
68	    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
69	    return floor($diff / 86400) . 'd ago';
70	}
71	
72	function generateToken(): string
73	{
74	    return bin2hex(random_bytes(32));
75	}
76	
77	/** Basic PHPMailer-free mail wrapper — swap in PHPMailer/SMTP for production */
78	function sendAppEmail(string $to, string $subject, string $body): bool
79	{
80	    $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
81	    $headers .= 'From: ' . SITE_NAME . ' <no-reply@tpp.local>' . "\r\n";
82	    return @mail($to, $subject, $body, $headers);
83	}
