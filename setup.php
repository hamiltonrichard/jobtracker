<?php 
/**
 * setup.php
 * Updated to include company/suite fields for contacts and 'Apply For' status.
 */
require_once 'config.php'; 

$setup_complete = false; 
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    $admin_user = trim($_POST['admin_user']);
    $admin_pass = $_POST['admin_pass'];

    if (empty($admin_user) || empty($admin_pass)) {
        $error = "Admin username and password are required.";
    } else {
        try {
            // 1. CREATE CORE TABLES
            $pdo->exec("CREATE TABLE IF NOT EXISTS statuses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status_name VARCHAR(50) NOT NULL UNIQUE
            ) ENGINE=InnoDB");

            // Updated contacts table schema with company and suite fields [40, Conversation History]
            $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                company_name VARCHAR(255),
                street_address VARCHAR(255),
                suite_number VARCHAR(50),
                city VARCHAR(100),
                state VARCHAR(50),
                zip VARCHAR(20),
                primary_phone VARCHAR(20),
                email VARCHAR(255),
                web_url VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");

            $pdo->exec("CREATE TABLE IF NOT EXISTS jobs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255) NOT NULL,
                job_title VARCHAR(255) NOT NULL,
                status_id INT NOT NULL,
                date_applied DATE NOT NULL,
                FOREIGN KEY (status_id) REFERENCES statuses(id)
            ) ENGINE=InnoDB");

            $pdo->exec("CREATE TABLE IF NOT EXISTS communications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                job_id INT NOT NULL,
                contact_email VARCHAR(255),
                comm_date DATE NOT NULL,
                FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
            ) ENGINE=InnoDB");

            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB");

            // 2. APPLY MIGRATIONS [42, Conversation History]
            $contact_cols = $pdo->query("SHOW COLUMNS FROM contacts")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('company_name', $contact_cols)) {
                $pdo->exec("ALTER TABLE contacts ADD COLUMN company_name VARCHAR(255) AFTER last_name");
            }
            if (!in_array('suite_number', $contact_cols)) {
                $pdo->exec("ALTER TABLE contacts ADD COLUMN suite_number VARCHAR(50) AFTER street_address");
            }

            $job_cols = $pdo->query("SHOW COLUMNS FROM jobs")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('interview_datetime', $job_cols)) {
                $pdo->exec("ALTER TABLE jobs ADD COLUMN interview_datetime DATETIME DEFAULT NULL");
            }
            if (!in_array('contact_id', $job_cols)) {
                $pdo->exec("ALTER TABLE jobs ADD COLUMN contact_id INT DEFAULT NULL, ADD FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL");
            }
            if (!in_array('job_description', $job_cols)) {
                $pdo->exec("ALTER TABLE jobs ADD COLUMN job_description TEXT AFTER date_applied");
            }
            if (!in_array('resume_path', $job_cols)) {
                $pdo->exec("ALTER TABLE jobs ADD COLUMN resume_path VARCHAR(255) DEFAULT NULL, ADD COLUMN cover_letter_path VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('notes', $job_cols)) {
                $pdo->exec("ALTER TABLE jobs ADD COLUMN notes TEXT");
            }

            $comm_cols = $pdo->query("SHOW COLUMNS FROM communications")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('comm_direction', $comm_cols)) {
                $pdo->exec("ALTER TABLE communications ADD COLUMN comm_direction ENUM('Inbound', 'Outbound') DEFAULT 'Outbound', ADD COLUMN comm_subject VARCHAR(255), ADD COLUMN comm_summary TEXT");
            }

            // 3. SEED STATUSES [43, Conversation History]
            // Added 'Apply For' to allow tracking jobs before application submission
            $statusStmt = $pdo->prepare("INSERT IGNORE INTO statuses (status_name) VALUES (?)");
            foreach (['Apply For', 'Applied', 'Interviewing', 'Rejected', 'Offer'] as $s) {
                $statusStmt->execute([$s]);
            }

            // 4. SEED ADMIN USER
            $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT IGNORE INTO users (username, password_hash) VALUES (?, ?)")->execute([$admin_user, $hash]);

            $setup_complete = true;
        } catch (PDOException $e) {
            die("Setup failed: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Setup</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f7fafc; margin: 0; font-family: sans-serif; }
        .setup-card { width: 500px; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        input { width: 100%; box-sizing: border-box; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="setup-card">
        <?php if ($setup_complete): ?>
            <h1 style="color: green; text-align: center;">Setup Successful</h1>
            <p>The database has been updated with Company and Suite fields for contacts, and the 'Apply For' status.</p>
            <a href="dashboard.php" class="btn" style="display: block; text-align: center; text-decoration: none; margin-top: 20px;">Go to Dashboard</a>
        <?php else: ?>
            <form method="POST">
                <h1 style="text-align: center;">Initial Setup</h1>
                <?php if ($error) echo "<p style='color:red; text-align:center;'>".htmlspecialchars($error)."</p>"; ?>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <label>Admin Username</label>
                <input type="text" name="admin_user" placeholder="e.g., admin" required>
                <label>Admin Password</label>
                <input type="password" name="admin_pass" placeholder="Enter secure password" required>
                <button type="submit" class="btn" style="width: 100%;">Initialize Application</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
