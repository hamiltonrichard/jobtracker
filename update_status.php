<?php require_once 'config.php'; checkLogin(); if ($_GET['token'] !== $_SESSION['csrf_token']) die("CSRF failed");
$stmt = $pdo->prepare("SELECT id FROM statuses WHERE status_name = ?"); $stmt->execute([$_GET['status']]); $s = $stmt->fetch();
if ($s) { $pdo->prepare("UPDATE jobs SET status_id = ? WHERE id = ?")->execute([$s['id'], $_GET['id']]); }
header("Location: dashboard.php"); exit; ?>
