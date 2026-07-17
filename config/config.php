1	<?php
2	/**
3	 * Global application configuration
4	 */
5	
6	// --- Session hardening: must run before session_start() ---
7	ini_set('session.cookie_httponly', 1);
8	ini_set('session.use_strict_mode', 1);
9	ini_set('session.cookie_samesite', 'Lax');
10	// ini_set('session.cookie_secure', 1); // enable once served over HTTPS
11	
12	if (session_status() === PHP_SESSION_NONE) {
13	    session_start();
14	}
15	
16	define('BASE_URL', 'http://localhost/tpp');
17	define('SITE_NAME', 'Training & Placement Portal');
18	define('COLLEGE_NAME', 'K.D. Polytechnic');
19	
20	define('UPLOAD_PATH_RESUMES',   __DIR__ . '/../assets/uploads/resumes/');
21	define('UPLOAD_PATH_LOGOS',     __DIR__ . '/../assets/uploads/company_logos/');
22	define('UPLOAD_PATH_OFFERS',    __DIR__ . '/../assets/uploads/offer_letters/');
23	define('UPLOAD_PATH_PHOTOS',    __DIR__ . '/../assets/uploads/profile_photos/');
24	
25	define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
26	
27	// Role IDs (must match `roles` table)
28	define('ROLE_SUPER_ADMIN', 1);
29	define('ROLE_TPO', 2);
30	define('ROLE_STUDENT', 3);
31	define('ROLE_COMPANY', 4);
32	
33	require_once __DIR__ . '/database.php';
34	require_once __DIR__ . '/../includes/functions.php';
35	require_once __DIR__ . '/../includes/security.php';
