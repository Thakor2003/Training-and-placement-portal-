1	<?php
2	/**
3	 * Database connection (PDO) — Training & Placement Portal
4	 * Uses prepared statements everywhere to prevent SQL injection.
5	 */
6	
7	define('DB_HOST', 'localhost');
8	define('DB_NAME', 'tpp_db');
9	define('DB_USER', 'root');
10	define('DB_PASS', '');       // set your XAMPP MySQL password here
11	define('DB_CHARSET', 'utf8mb4');
12	
13	function getDB(): PDO
14	{
15	    static $pdo = null;
16	
17	    if ($pdo === null) {
18	        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
19	        $options = [
20	            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
21	            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
22	            PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
23	        ];
24	
25	        try {
26	            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
27	        } catch (PDOException $e) {
28	            error_log('DB Connection failed: ' . $e->getMessage());
29	            die('A system error occurred. Please try again later.');
30	        }
31	    }
32	
33	    return $pdo;
34	}
