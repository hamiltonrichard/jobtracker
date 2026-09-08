<?php
/**
 * user_management.php
 * Updated with aligned, standardized 500px input boxes.
 */
require_once 'config.php';
checkLogin();
include 'header.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $new_username = trim($_POST['username']);
        $new_password = $_POST['password'];

        if (!empty($new_username) && !empty($new_password)) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                $stmt->execute([$new_username, $hash]);
                $message = "User '" . htmlspecialchars($new_username) . "' added successfully.";
            } catch (PDOException $e) {
                $error = "Error adding user: Username may already exist.";
            }
        } else {
            $error = "All fields are required.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];

        if (!empty($new_password)) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $user_id]);
            $message = "Password updated successfully.";
        } else {
            $error = "New password cannot be empty.";
        }
    }
}

$all_users = $pdo->query("SELECT id, username FROM users")->fetchAll();
?>

<div style="display: flex; flex-direction: column; align-items: center;">
    <h1>User Management</h1>

    <?php if ($message) echo "<p style='color: green;'>$message</p>"; ?>
    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>

    <!-- Standardized Add User Section -->
    <div class="calendar-card" style="width: 500px;">
        <h3>Add New User</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="add_user">
            
            <label>New Username</label>
            <input type="text" name="username" placeholder="Enter username" required>
            
            <label>New Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            
            <button type="submit" class="btn" style="width: 100%;">Create User</button>
        </form>
    </div>

    <!-- Standardized Reset Section -->
    <div class="calendar-card" style="width: 100%; max-width: 900px; margin-top: 20px;">
        <h3>Reset User Password</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>New Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $user): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="reset_password">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <!-- Inline override to keep table-based inputs from expanding to 500px if space is tight -->
                            <input type="password" name="new_password" placeholder="New Password" required style="width: 250px; margin-bottom: 0;">
                        </td>
                        <td><button type="submit" class="btn">Update</button></td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
