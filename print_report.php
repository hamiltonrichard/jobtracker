<?php
/**
 * print_report.php
 * A minimal, printer-friendly version of the job report.
 */
require_once 'config.php';
checkLogin();

$selected_status = $_GET['status_id'] ?? null;

// Fetch status name for the header
$status_name = "All Statuses";
if ($selected_status) {
    $stmt = $pdo->prepare("SELECT status_name FROM statuses WHERE id = ?");
    $stmt->execute([$selected_status]);
    $res = $stmt->fetch();
    $status_name = $res['status_name'] ?? "All Statuses";
}

// Fetch the filtered data [29, Conversation History]
$sql = "SELECT j.*, s.status_name FROM jobs j JOIN statuses s ON j.status_id = s.id";
if ($selected_status) {
    $sql .= " WHERE j.status_id = :status_id";
}
$sql .= " ORDER BY j.date_applied DESC";

$stmt = $pdo->prepare($sql);
if ($selected_status) { $stmt->execute(['status_id' => $selected_status]); } 
else { $stmt->execute(); }
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Report - <?= htmlspecialchars($status_name) ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; background: #fff; margin: 20px; }
        .report-header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 30px; padding-bottom: 10px; }
        .job-entry { margin-bottom: 40px; page-break-inside: avoid; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .status-label { font-weight: bold; text-transform: uppercase; font-size: 0.8em; }
        .section-title { font-weight: bold; display: block; margin-top: 10px; text-decoration: underline; }
        .content-box { margin-top: 5px; white-space: pre-wrap; font-size: 0.95em; }
        
        /* Hide buttons when printing */
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()"> <!-- Automatically opens print dialog on load -->

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Now</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close Window</button>
    </div>

    <div class="report-header">
        <h1>Job Application Tracker Report</h1>
        <p>Filter: <strong><?= htmlspecialchars($status_name) ?></strong> | Generated: <?= date('Y-m-d H:i') ?></p>
    </div>

    <?php foreach ($jobs as $job): ?>
        <div class="job-entry">
            <h2 style="margin: 0;"><?= htmlspecialchars($job['job_title']) ?> @ <?= htmlspecialchars($job['company_name']) ?></h2>
            <p style="margin: 5px 0;">
                <span class="status-label">Status:</span> <?= $job['status_name'] ?> | 
                <span class="status-label">Applied:</span> <?= $job['date_applied'] ?>
            </p>

            <span class="section-title">Job Description:</span>
            <div class="content-box"><?= htmlspecialchars($job['job_description'] ?: 'No description provided.') ?></div>

            <span class="section-title">Personal Notes & Research:</span>
            <div class="content-box" style="font-style: italic;">
                <?= !empty($job['notes']) ? htmlspecialchars($job['notes']) : "No personal notes recorded." ?>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>
