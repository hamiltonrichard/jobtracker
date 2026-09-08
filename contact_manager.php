<?php 
require_once 'config.php'; 
checkLogin(); 
include 'header.php'; 

$edit_id = $_GET['edit_id'] ?? null; 
$d = [
    'first_name'=>'', 'last_name'=>'', 'company_name'=>'', 'email'=>'', 
    'primary_phone'=>'', 'street_address'=>'', 'suite_number'=>'', 
    'city'=>'', 'state'=>'', 'zip'=>'', 'web_url'=>''
]; 

if ($edit_id) { 
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?"); 
    $stmt->execute([$edit_id]); 
    $d = $stmt->fetch() ?: $d; 
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF failed"); 
    $p = [
        $_POST['first_name'], $_POST['last_name'], $_POST['company_name'], 
        $_POST['email'], $_POST['primary_phone'], $_POST['street_address'], 
        $_POST['suite_number'], $_POST['city'], $_POST['state'], 
        $_POST['zip'], $_POST['web_url']
    ]; 
    if ($edit_id) { 
        $sql = "UPDATE contacts SET first_name=?, last_name=?, company_name=?, email=?, primary_phone=?, street_address=?, suite_number=?, city=?, state=?, zip=?, web_url=? WHERE id=?"; 
        $p[] = $edit_id; 
    } else { 
        $sql = "INSERT INTO contacts (first_name, last_name, company_name, email, primary_phone, street_address, suite_number, city, state, zip, web_url) VALUES (?,?,?,?,?,?,?,?,?,?,?)"; 
    } 
    $pdo->prepare($sql)->execute($p); 
    header("Location: contact_manager.php"); 
    exit; 
} 
?> 

<div style="display:flex; flex-direction:column; align-items:flex-start;">
    <h1>Contact Manager</h1>
    <form method="POST" style="display: flex; flex-direction: column; width: 500px; gap: 15px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> 
        <div style="display: flex; gap: 10px; width: 100%;">
            <div style="flex: 1;"><label>First Name</label><input type="text" name="first_name" value="<?= htmlspecialchars($d['first_name']) ?>" required style="width:100%;"></div>
            <div style="flex: 1;"><label>Last Name</label><input type="text" name="last_name" value="<?= htmlspecialchars($d['last_name']) ?>" required style="width:100%;"></div>
        </div>
        <label>Company Name</label><input type="text" name="company_name" value="<?= htmlspecialchars($d['company_name']) ?>" style="width:100%;">
        <div style="display: flex; gap: 10px; width: 100%;">
            <div style="flex: 1;"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($d['email']) ?>" style="width:100%;"></div>
            <div style="flex: 1;"><label>Phone</label><input type="text" name="primary_phone" value="<?= htmlspecialchars($d['primary_phone']) ?>" style="width:100%;"></div>
        </div>
        <div style="display: flex; gap: 10px; width: 100%;">
            <div style="flex: 2;"><label>Street Address</label><input type="text" name="street_address" value="<?= htmlspecialchars($d['street_address']) ?>" style="width:100%;"></div>
            <div style="flex: 1;"><label>Suite / Room #</label><input type="text" name="suite_number" value="<?= htmlspecialchars($d['suite_number']) ?>" style="width:100%;"></div>
        </div>
        <div style="display: flex; gap: 10px; width: 100%;">
            <div style="flex: 2;"><label>City</label><input type="text" name="city" value="<?= htmlspecialchars($d['city']) ?>" style="width:100%;"></div>
            <div style="flex: 1;"><label>State</label><input type="text" name="state" value="<?= htmlspecialchars($d['state']) ?>" style="width:100%;"></div>
            <div style="flex: 1;"><label>Zip</label><input type="text" name="zip" value="<?= htmlspecialchars($d['zip']) ?>" style="width:100%;"></div>
        </div>
        <label>Website URL</label><input type="text" name="web_url" value="<?= htmlspecialchars($d['web_url']) ?>" style="width:100%;">
        <button type="submit" class="btn" style="width: 150px;">Save Contact</button>
    </form>

    <h2 style="margin-top: 40px;">Contact List</h2>
    <table style="width: 100%; max-width: 1000px;">
        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Work Address</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($pdo->query("SELECT * FROM contacts ORDER BY last_name ASC") as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['last_name'].", ".$c['first_name']) ?></td>
                <td><?= htmlspecialchars($c['primary_phone']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td style="font-size: 0.9em;">
                    <?= htmlspecialchars($c['street_address']) ?><?= $c['suite_number'] ? " Ste ".$c['suite_number'] : "" ?><br>
                    <span style="color: #4a5568;"><?= htmlspecialchars($c['city'].", ".$c['state']." ".$c['zip']) ?></span>
                </td>
                <td><a href="?edit_id=<?= $c['id'] ?>">Edit</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
