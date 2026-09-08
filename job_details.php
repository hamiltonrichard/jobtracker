<?php 
/**
 * job_details.php
 * Displays comprehensive details for a specific position, including documents,
 * interaction logs, and expanded contact information.
 */
require 'config.php'; 
checkLogin(); 
include 'header.php';

$id = $_GET['id'];

// Updated SQL to fetch expanded contact details based on recent schema updates [11, 40, Conversation History]
$sql = "SELECT j.*, s.status_name, 
               c.first_name, c.last_name, c.company_name AS contact_company, 
               c.email AS contact_email, c.primary_phone, 
               c.street_address, c.suite_number, c.city, c.state, c.zip, c.web_url
        FROM jobs j
        JOIN statuses s ON j.status_id = s.id
        LEFT JOIN contacts c ON j.contact_id = c.id
        WHERE j.id = ?";

$stmt = $pdo->prepare($sql); 
$stmt->execute([$id]); 
$job = $stmt->fetch();

if (!$job) { 
    die("Job not found."); 
}

// Fetch communication logs [1, 2]
$comms = $pdo->prepare("SELECT * FROM communications WHERE job_id = ? ORDER BY comm_date DESC"); 
$comms->execute([$id]); 
$logs = $comms->fetchAll(); 
?>

<h1>Details: <?= htmlspecialchars($job['company_name']) ?></h1>

<!-- Layout Grid for Position and Contact Info [2] --> 
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
    <div class="calendar-card">
        <h3>Position Info</h3>
        <p><strong>Title:</strong> <?= htmlspecialchars($job['job_title']) ?></p>
        <p><strong>Status:</strong> <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $job['status_name'])) ?>"><?= $job['status_name'] ?></span></p>
        <p><strong>Interview:</strong> <?= $job['interview_datetime'] ?: 'Not Scheduled' ?></p>
        <p><strong>Documents:</strong><br>
            <?= $job['resume_path'] ? "<a href='{$job['resume_path']}' target='_blank'>View Resume</a>" : "No Resume" ?> |
            <?= $job['cover_letter_path'] ? "<a href='{$job['cover_letter_path']}' target='_blank'>View Cover Letter</a>" : "No Cover Letter" ?>
        </p>
    </div>

    <!-- Updated Contact Details Card with expanded address and phone [13, Conversation History] -->
    <div class="calendar-card">
        <h3>Contact Details</h3>
        <?php if ($job['contact_id']): ?>
            <p><strong>Name:</strong> <?= htmlspecialchars($job['first_name'].' '. $job['last_name']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($job['primary_phone'] ?: 'N/A') ?></p>
            <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($job['contact_email']) ?>"><?= htmlspecialchars($job['contact_email']) ?></a></p>
            
            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
            
            <p><strong>Company:</strong> <?= htmlspecialchars($job['contact_company'] ?: 'N/A') ?></p>
            <p><strong>Work Address:</strong><br>
                <?php if ($job['street_address']): ?>
                    <?= htmlspecialchars($job['street_address']) ?><?= $job['suite_number'] ? " Ste ".htmlspecialchars($job['suite_number']) : "" ?><br>
                    <?= htmlspecialchars($job['city'].", ".$job['state']." ".$job['zip']) ?>
                <?php else: ?>
                    No address recorded.
                <?php endif; ?>
            </p>
            
            <?php if ($job['web_url']): ?>
                <p><strong>Website:</strong> <a href="<?= htmlspecialchars($job['web_url']) ?>" target="_blank">Visit Site</a></p>
            <?php endif; ?>
        <?php else: ?>
            <p>No contact linked. <a href="job_form.php?id=<?= $id ?>">Link one now.</a></p>
        <?php endif; ?>
    </div>
</div>

<!-- Job Description Section [3] --> 
<div class="calendar-card">
    <h3>Job Description</h3>
    <div style="white-space: pre-wrap; color: #4a5568;"><?= htmlspecialchars($job['job_description']) ?></div>
</div>

<!-- Personal Notes Section [4] --> 
<div class="calendar-card" style="border-top: 4px solid #2b6cb0;">
    <h3>Personal Notes & Research</h3>
    <div style="white-space: pre-wrap; font-style: italic; color: #2d3748;">
        <?= !empty($job['notes']) ? nl2br(htmlspecialchars($job['notes'])) : "No notes recorded for this position." ?>
    </div>
</div>

<!-- Interaction Logs [4] --> 
<h3>Interaction Logs</h3>
<?php if ($logs): ?>
    <?php foreach ($logs as $l): ?>
        <div class="calendar-card" style="border-left: 5px solid <?= $l['comm_direction']=='Inbound'?'#38a169':'#2b6cb0' ?>;">
            <strong><?= $l['comm_direction'] ?> | <?= $l['comm_date'] ?></strong>: <?= htmlspecialchars($l['comm_subject']) ?><br>
            <p style="margin-top: 10px;"><?= nl2br(htmlspecialchars($l['comm_summary'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No communications logged yet.</p>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="add_comm.php?job_id=<?= $id ?>" class="btn">Add New Log</a>
    <a href="job_form.php?id=<?= $id ?>" class="btn" style="background-color: #4a5568; margin-left: 10px;">Edit Position</a>
</div>

<?php include 'footer.php'; ?>
