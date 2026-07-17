1	<?php
2	require_once __DIR__ . '/../config/config.php';
3	
4	$_SESSION = [];
5	session_destroy();
6	session_start();
7	redirect('auth/login.php');
