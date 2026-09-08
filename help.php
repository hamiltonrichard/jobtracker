<?php 
require_once 'config.php'; include 'header.php';
$t = $_GET['topic'] ?? 'dashboard.php';
$h = [
    'dashboard.php' => 'Watch for 30-day alerts for "Applied" jobs. "Apply For" jobs are for tracking positions you intend to pursue later.',
    'job_form.php' => 'Use "Apply For" to save details before applying. Switch to "Applied" once submitted to start the 30-day follow-up tracker.',
    'contact_manager.php' => 'Manage interviewers and recruiters, including their company and work address.'
];
echo "<h1>Help</h1><p>".($h[$t] ?? "No specific help for this page.").")</p>";
include 'footer.php'; 
?>
