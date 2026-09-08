<?php require 'config.php'; checkLogin(); header('Content-Type: application/sql'); header('Content-Disposition: attachment; filename="backup_'.date('Y-m-d').'.sql"');
foreach (['statuses', 'contacts', 'jobs', 'communications', 'users'] as $t) { $s = $pdo->query("SHOW CREATE TABLE $t")->fetch(PDO::FETCH_NUM); echo "\n\n".$s[50].";\n\n";
foreach ($pdo->query("SELECT * FROM $t")->fetchAll(PDO::FETCH_ASSOC) as $r) { $k = array_keys($r); $v = array_map(function($val) use ($pdo) { return $pdo->quote($val); }, array_values($r));
echo "INSERT INTO $t (".implode(', ', $k).") VALUES (".implode(', ', $v).");\n"; } } ?>
