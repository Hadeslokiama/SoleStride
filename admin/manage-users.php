<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin-header.php';

global $conn;

$current_admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';
$allowed_roles = ['super_admin', 'inventory_manager', 'staff'];

// Only super_admin may create or modify admin accounts.
$self_stmt = mysqli_prepare($conn, "SELECT role FROM admins WHERE id = ?");
mysqli_stmt_bind_param($self_stmt, "i", $current_admin_id);
mysqli_stmt_execute($self_stmt);
$self_result = mysqli_stmt_get_result($self_stmt);
$self = mysqli_fetch_assoc($self_result);
mysqli_stmt_close($self_stmt);
$is_super_admin = ($self['role'] === 'super_admin');

if (!$is_super_admin) {
    echo '<p class="access-denied">Access restricted to super admins.</p>';
    echo '</main></div></body></html>';
    exit;
}

if (empty($_SESSION['manage_users_csrf'])) {
    $_SESSION['manage_users_csrf'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['manage_users_csrf'];

// Handle admin account creation and updates.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $posted_token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($csrf_token, $posted_token)) {
        $error = 'Invalid form token. Please refresh and try again.';
    } elseif ($_POST['action'] === 'create_admin') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $new_role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($full_name === '' || strlen($full_name) > 150) {
            $error = 'Enter a valid full name up to 150 characters.';
        } elseif (!validate_email($email) || strlen($email) > 150) {
            $error = 'Enter a valid email address.';
        } elseif (!in_array($new_role, $allowed_roles, true)) {
            $error = 'Choose a valid role.';
        } elseif (!validate_password_strength($password)) {
            $error = 'Password must be at least 8 characters and include at least one letter and one number.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            $existing_stmt = mysqli_prepare($conn, "SELECT id FROM admins WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($existing_stmt, "s", $email);
            mysqli_stmt_execute($existing_stmt);
            mysqli_stmt_store_result($existing_stmt);
            $email_exists = mysqli_stmt_num_rows($existing_stmt) > 0;
            mysqli_stmt_close($existing_stmt);

            if ($email_exists) {
                $error = 'An admin account already exists for that email.';
            } else {
                $password_hash = hash_password($password);
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO admins (full_name, email, password_hash, role, created_by)
                     VALUES (?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $password_hash, $new_role, $current_admin_id);

                if (mysqli_stmt_execute($stmt)) {
                    $new_admin_id = mysqli_insert_id($conn);
                    log_admin_action($conn, $current_admin_id, 'create_admin', 'admins', $new_admin_id, "Role: $new_role");
                    $success = 'Admin account created.';
                } else {
                    $error = 'Failed to create admin account.';
                }

                mysqli_stmt_close($stmt);
            }
        }
    } elseif ($_POST['action'] === 'update_role') {
        $target_id = (int) ($_POST['admin_id'] ?? 0);
        $new_role = $_POST['new_role'] ?? '';

        if ($target_id <= 0 || !in_array($new_role, $allowed_roles, true)) {
            $error = 'Choose a valid role.';
        } elseif ($target_id === $current_admin_id && $new_role !== 'super_admin') {
            $error = 'You cannot remove your own super admin role.';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE admins SET role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_role, $target_id);

            if (mysqli_stmt_execute($stmt)) {
                log_admin_action($conn, $current_admin_id, 'update_role', 'admins', $target_id, "New role: $new_role");
                $success = 'Role updated.';
            } else {
                $error = 'Failed to update role.';
            }

            mysqli_stmt_close($stmt);
        }
    } elseif ($_POST['action'] === 'toggle_active') {
        $target_id = (int) ($_POST['admin_id'] ?? 0);

        if ($target_id <= 0) {
            $error = 'Choose a valid admin account.';
        } elseif ($target_id === $current_admin_id) {
            $error = 'You cannot disable your own admin account.';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE admins SET is_active = NOT is_active WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $target_id);

            if (mysqli_stmt_execute($stmt)) {
                log_admin_action($conn, $current_admin_id, 'toggle_active', 'admins', $target_id);
                $success = 'Status updated.';
            } else {
                $error = 'Failed to update status.';
            }

            mysqli_stmt_close($stmt);
        }
    }
}

$admins_stmt = mysqli_prepare($conn, "SELECT id, full_name, email, role, is_active FROM admins ORDER BY id");
mysqli_stmt_execute($admins_stmt);
$admins_result = mysqli_stmt_get_result($admins_stmt);
mysqli_stmt_close($admins_stmt);
?>

<h1>Manage Admin Users</h1>

<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

<h2>Add Admin User</h2>
<form method="post" class="admin-form admin-create-form" novalidate>
    <input type="hidden" name="action" value="create_admin">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <p>
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" maxlength="150" required>
    </p>

    <p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="150" required>
    </p>

    <p>
        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="staff">staff</option>
            <option value="inventory_manager">inventory_manager</option>
            <option value="super_admin">super_admin</option>
        </select>
    </p>

    <p>
        <label for="password">Temporary Password</label>
        <input type="password" id="password" name="password" required>
    </p>

    <p>
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </p>

    <p>
        <button type="submit" class="btn btn-primary">Add Admin</button>
    </p>
</form>

<table class="admin-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($admin = mysqli_fetch_assoc($admins_result)): ?>
            <tr>
                <td><?= htmlspecialchars($admin['full_name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="admin_id" value="<?= (int) $admin['id'] ?>">
                        <select name="new_role">
                            <?php foreach (['super_admin', 'inventory_manager', 'staff'] as $r): ?>
                                <option value="<?= $r ?>" <?= $admin['role'] === $r ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td><?= $admin['is_active'] ? 'Active' : 'Disabled' ?></td>
                <td>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="action" value="toggle_active">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="admin_id" value="<?= (int) $admin['id'] ?>">
                        <button type="submit"><?= $admin['is_active'] ? 'Disable' : 'Enable' ?></button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </main>
</div>
</body>
</html>
