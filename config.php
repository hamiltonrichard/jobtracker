<?php
date_default_timezone_set('America/Chicago'); 
session_start();
$host = 'localhost'; $db = 'job_tracker_db'; $user = 'job_tracker'; $pass = 'password'; $charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
try { 
    $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (\PDOException $e) { 
    error_log($e->getMessage()); 
    die("A database error occurred. Please try again later."); 
}
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
function checkLogin() { if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; } }
?>
