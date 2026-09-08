<?php
/**
 * header.php
 * Defines the application sidebar, navigation workflow, and global head section.
 */
$current_page = basename($_SERVER['PHP_SELF']);

// Standardized Application Workflow updated with new features [37, Conversation History]
$workflow = [
    'dashboard.php'       => 'Dashboard',
    'contact_manager.php' => 'Contact Manager',
    'job_form.php'        => 'Add New Job',
    'job_report.php'      => 'Job Reports',
    'backup.php'          => 'Database Backup',
    'restore.php'         => 'Database Restore',
    'user_management.php' => 'User Management'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Tracker</title>
    <!-- Standardized Global Stylesheet [4] -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Sidebar Navigation (Anchored Left) [7] -->
<div class="sidebar">
    <h3>Job Tracker</h3>
    <ul>
        <?php foreach ($workflow as $file => $name): ?>
            <!-- Highlights the current active page in the sidebar -->
            <li class="<?= ($current_page == $file) ? 'active' : '' ?>">
                <a href="<?= $file ?>"><?= $name ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Contextual Help Section [7] -->
    <div class="help-section">
        <hr style="border: 0; border-top: 1px solid #4a5568; margin: 20px 0;">
        <a href="help.php?topic=<?= $current_page ?>">Help</a>
    </div>

    <!-- Secure Logout Button [8] -->
    <div class="logout-section">
        <a href="logout.php">Logout</a>
    </div>

<!-- Restored Logout Button [1] -->
<div class="logout-section" style="margin-top: 20px;">
    <hr style="border: 0; border-top: 1px solid #4a5568; margin-bottom: 20px;">
    <a href="logout.php" style="color: #feb2b2; text-decoration: none; font-weight: bold;">Logout</a>
</div>
</div>

<!-- Main Content Area: Opened here, closed in footer.php [8] -->
<div class="main-content">
