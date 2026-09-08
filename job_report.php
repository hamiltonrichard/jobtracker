<?php
/**
 * job_report.php
 * Generates a filtered report of job applications including descriptions and notes.
 */
require_once 'config.php';
checkLogin(); // Ensures only authenticated users access the report [1]
include 'header.php';

// Get filter criteria from the URL
$selected_status = $_GET['status_id'] ?? null;

// 1. Fetch all available statuses for the dropdown filter
$statuses = $pdo->query("SELECT * FROM statuses ORDER BY status_name ASC")->fetchAll();

// 2. Build the query to fetch jobs, including descriptions and notes [51, Conversation History]
$sql = "SELECT j.*, s.status_name 
        FROM jobs j 
        JOIN statuses s ON j.status_id = s.id";

if ($selected_status) {
    $sql .= " WHERE j.status_id = :status_id";
}
$sql .= " ORDER BY j.date_applied DESC";

$stmt = $pdo->prepare($sql);
if ($selected_status) {
    $stmt->execute(['status_id' => $selected_status]);
} else {
    $stmt->execute();
}
$report_jobs = $stmt->fetchAll();
?>

<div style="display: flex; flex-direction: column; align-items: center;">
    <h1>Job Application Report</h1>

    <!-- Filter Section (Standardized 500px Width [2]) -->
    <div class="calendar-card" style="width: 500px;">
        <form method="GET" style="display: flex; flex-direction: column;">
            <label for="status_id">Filter by Status:</label>
            <select name="status_id" id="status_id" onchange="this.form.submit()">
                <option value="">-- Show All Statuses --</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $selected_status == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['status_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn">Filter</button></noscript>
        </form>

        <!-- New: Printer Friendly Button added in latest update [Conversation History] -->
        <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
            <a href="print_report.php?status_id=<?= $selected_status ?>" 
               target="_blank" 
               class="btn" 
               style="background-color: #4a5568; font-size: 0.9em;">
               🖨️ Print This Report
            </a>
        </div>
    </div>

    <!-- Report Results Container -->
    <div style="width: 100%; max-width: 900px; margin-top: 20px;">
        <?php if ($report_jobs): ?>
            <?php foreach ($report_jobs as $job): ?>
                <div class="calendar-card" style="margin-bottom: 30px; border-left: 5px solid #2b6cb0;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <h3 style="margin: 0;">
                            <a href="job_details.php?id=<?= $job['id'] ?>" style="color: #2d3748; text-decoration: none;">
                                <?= htmlspecialchars($job['job_title']) ?> @ <?= htmlspecialchars($job['company_name']) ?>
                            </a>
                        </h3>
                        <!-- Color-Coded Status Badge [3] -->
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $job['status_name'])) ?>">
                            <?= $job['status_name'] ?>
                        </span>
                    </div>
                    <p style="font-size: 0.85em; color: #718096; margin-top: 5px;">Applied: <?= $job['date_applied'] ?></p>
                    
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 15px 0;">
                    
                    <strong>Job Description:</strong>
                    <div style="white-space: pre-wrap; color: #4a5568; margin-bottom: 15px; font-size: 0.95em;">
                        <?= !empty($job['job_description']) ? htmlspecialchars($job['job_description']) : "<em>No description provided.</em>" ?>
                    </div>

                    <strong>Personal Notes & Research:</strong>
                    <div style="white-space: pre-wrap; background: #f7fafc; padding: 10px; border-radius: 4px; border: 1px solid #edf2f7; font-style: italic; color: #2d3748;">
                        <?= !empty($job['notes']) ? nl2br(htmlspecialchars($job['notes'])) : "No notes recorded for this position." ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #718096;">No jobs found matching the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
