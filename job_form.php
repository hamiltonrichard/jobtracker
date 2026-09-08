<?php 
/**
 * job_form.php
 * Updated to fix layout spacing issues in the file upload section.
 */
require_once 'config.php'; 
checkLogin(); 
include 'header.php';

$id = $_GET['id'] ?? null;

$job_data = [
    'company_name' => '',
    'job_title' => '',
    'status_id' => '',
    'contact_id' => '',
    'date_applied' => date('Y-m-d'),
    'interview_datetime' => '',
    'job_description' => '',
    'notes' => '',
    'resume_path' => '',
    'cover_letter_path' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->execute([$id]);
    $job_data = $stmt->fetch() ?: $job_data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF validation failed.");

    $res_path = $job_data['resume_path'];
    if (!empty($_FILES['resume']['name'])) {
        $res_path = 'uploads/' . time() . '_res_' . $_FILES['resume']['name'];
        move_uploaded_file($_FILES['resume']['tmp_name'], $res_path);
    }

    $cov_path = $job_data['cover_letter_path'];
    if (!empty($_FILES['cover_letter']['name'])) {
        $cov_path = 'uploads/' . time() . '_cov_' . $_FILES['cover_letter']['name'];
        move_uploaded_file($_FILES['cover_letter']['tmp_name'], $cov_path);
    }

    $params = [
        $_POST['company_name'],
        $_POST['job_title'],
        $_POST['status_id'],
        !empty($_POST['contact_id']) ? $_POST['contact_id'] : null,
        $_POST['date_applied'],
        !empty($_POST['interview_datetime']) ? $_POST['interview_datetime'] : null,
        $_POST['job_description'],
        $_POST['notes'],
        $res_path,
        $cov_path
    ];

    if ($id) {
        $sql = "UPDATE jobs SET company_name=?, job_title=?, status_id=?, contact_id=?, 
                date_applied=?, interview_datetime=?, job_description=?, notes=?, 
                resume_path=?, cover_letter_path=? WHERE id=?";
        $params[] = $id;
    } else {
        $sql = "INSERT INTO jobs (company_name, job_title, status_id, contact_id, 
                date_applied, interview_datetime, job_description, notes, 
                resume_path, cover_letter_path) VALUES (?,?,?,?,?,?,?,?,?,?)";
    }
    $pdo->prepare($sql)->execute($params);
    header("Location: dashboard.php");
    exit;
}

$statuses = $pdo->query("SELECT * FROM statuses ORDER BY status_name ASC")->fetchAll(); 
$contacts = $pdo->query("SELECT id, first_name, last_name, company_name FROM contacts ORDER BY last_name ASC")->fetchAll(); 
?>

<div style="display: flex; flex-direction: column; align-items: center; margin-top: 20px;">
    <h1><?= $id ? 'Edit' : 'Add' ?> Application</h1>

    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; width: 500px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Company Name</label>
        <input type="text" name="company_name" value="<?= htmlspecialchars($job_data['company_name']) ?>" required>

        <label>Job Title</label>
        <input type="text" name="job_title" value="<?= htmlspecialchars($job_data['job_title']) ?>" required>

        <label>Status</label>
        <select name="status_id" required>
            <option value="">-- Select Status --</option>
            <?php foreach($statuses as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $job_data['status_id']==$s['id']?'selected':'' ?>>
                    <?= htmlspecialchars($s['status_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Primary Contact</label>
        <select name="contact_id">
            <option value="">-- No Contact Linked --</option>
            <?php foreach($contacts as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $job_data['contact_id']==$c['id']?'selected':'' ?>>
                    <?= htmlspecialchars($c['last_name'].", ".$c['first_name']) ?>
                    <?= !empty($c['company_name']) ? " (".htmlspecialchars($c['company_name']).")" : "" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Date Applied</label>
        <input type="date" name="date_applied" value="<?= htmlspecialchars($job_data['date_applied']) ?>" required>

        <label>Interview Date & Time</label>
        <input type="datetime-local" name="interview_datetime" value="<?= $job_data['interview_datetime'] ? date('Y-m-d\TH:i', strtotime($job_data['interview_datetime'])) : '' ?>">

        <label>Job Description</label>
        <textarea name="job_description" rows="4"><?= htmlspecialchars($job_data['job_description']) ?></textarea>

        <label>Personal Notes</label>
        <textarea name="notes" rows="6" placeholder="Add any private notes..."><?= htmlspecialchars($job_data['notes']) ?></textarea>

        <!-- Fixed Spacing for Resume Section [24, Conversation History] -->
        <label>Resume (PDF)</label>
        <input type="file" name="resume" style="margin-bottom: 20px;">
        <p style="font-size: 0.8em; color: #718096; margin-top: -15px; margin-bottom: 20px;">
            Current: <?= htmlspecialchars(basename($job_data['resume_path'])) ?: 'None' ?>
        </p>

        <!-- Fixed Spacing for Cover Letter Section [24, Conversation History] -->
        <label>Cover Letter (PDF)</label>
        <input type="file" name="cover_letter" style="margin-bottom: 20px;">
        <p style="font-size: 0.8em; color: #718096; margin-top: -15px; margin-bottom: 25px;">
            Current: <?= htmlspecialchars(basename($job_data['cover_letter_path'])) ?: 'None' ?>
        </p>

        <button type="submit" class="btn">Save Application</button>
        <a href="dashboard.php" style="text-align: center; margin-top: 15px; color: #718096;">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
