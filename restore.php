<?php require 'config.php'; checkLogin(); include 'header.php'; if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['backup_file'])) {
if (pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION) == 'sql') { $pdo->exec(file_get_contents($_FILES['backup_file']['tmp_name'])); echo "<p>Restore successful.</p>"; } } ?>
<form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"><input type="file" name="backup_file" required><button type="submit" class="btn">Restore Database</button></form>
