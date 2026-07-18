<?php
require_once __DIR__ . '/includes/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $full_name = clean($conn, $_POST['full_name']);
    $email     = clean($conn, $_POST['email']);
    $username  = clean($conn, $_POST['username']);
    $password  = $_POST['password'];
    $role      = clean($conn, $_POST['role']);

    if ($full_name === '') $errors[] = "Admin full name is required.";
    if (!is_valid_email($email)) $errors[] = "Please enter a valid e-mail address.";
    if ($username === '' || !preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) $errors[] = "Username must be 3-30 letters, numbers, or underscores.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";

    if (empty($errors)) {
        $dupe = mysqli_query($conn, "SELECT id FROM admins WHERE username = '$username' OR email = '$email'");
        if ($dupe && mysqli_num_rows($dupe) > 0) {
            $errors[] = "That username or e-mail is already in use.";
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (full_name, email, username, password, role, status)
                VALUES ('$full_name', '$email', '$username', '$hashed', '$role', 'Active')";
        if (mysqli_query($conn, $sql)) {
            log_action($conn, $_SESSION['admin_name'], "Created new admin account '$username' with role '$role'.");
            $success = "Admin account '$username' created.";
        } else {
            $errors[] = "Could not create admin account.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = intval($_POST['admin_id']);
    $new_status = clean($conn, $_POST['new_status']);
    if ($id !== intval($_SESSION['admin_id'])) {
        mysqli_query($conn, "UPDATE admins SET status = '$new_status' WHERE id = $id");
        log_action($conn, $_SESSION['admin_name'], "Set admin #$id status to '$new_status'.");
    } else {
        $errors[] = "You can't deactivate your own account while logged in.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $id = intval($_POST['admin_id']);
    $role = clean($conn, $_POST['role']);
    mysqli_query($conn, "UPDATE admins SET role = '$role' WHERE id = $id");
    log_action($conn, $_SESSION['admin_name'], "Changed role of admin #$id to '$role'.");
}

$admins_result = mysqli_query($conn, "SELECT * FROM admins ORDER BY id");
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
    <h1 class="h2 fw-bold text-white">System Admin Role Provisioning</h1>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card p-4 mb-4">
    <h5 class="text-white mb-3">Add Administrative Account</h5>
    <form method="POST" class="row g-3">
        <div class="col-md-3"><input type="text" name="full_name" class="form-control" placeholder="Admin Full Name" required></div>
        <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email Address" required></div>
        <div class="col-md-2"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
        <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
        <div class="col-md-2">
            <select name="role" class="form-select">
                <option>Admin</option>
                <option>Manager</option>
                <option>Super Admin</option>
            </select>
        </div>
        <div class="col-12"><button type="submit" name="add_admin" class="btn key-btn btn-primary">Create Access Account</button></div>
    </form>
</div>

<div class="card p-4">
    <h5 class="text-white mb-3">Existing Admin Accounts</h5>
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($a = mysqli_fetch_assoc($admins_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['full_name']); ?><?= $a['id'] == $_SESSION['admin_id'] ? ' <span class="small text-muted-custom">(you)</span>' : ''; ?></td>
                        <td class="font-monospace small"><?= htmlspecialchars($a['username']); ?></td>
                        <td><?= htmlspecialchars($a['email']); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-2 align-items-center m-0">
                                <input type="hidden" name="admin_id" value="<?= $a['id']; ?>">
                                <select name="role" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
                                    <?php foreach (['Admin','Manager','Super Admin'] as $r): ?>
                                        <option <?= $a['role'] === $r ? 'selected' : ''; ?>><?= $r; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="change_role" value="1">
                            </form>
                        </td>
                        <td><span class="status-pill <?= $a['status'] === 'Active' ? 'status-active' : 'status-inactive'; ?>"><?= htmlspecialchars($a['status']); ?></span></td>
                        <td>
                            <?php if ($a['id'] != $_SESSION['admin_id']): ?>
                                <form method="POST" class="m-0">
                                    <input type="hidden" name="admin_id" value="<?= $a['id']; ?>">
                                    <input type="hidden" name="new_status" value="<?= $a['status'] === 'Active' ? 'Inactive' : 'Active'; ?>">
                                    <button type="submit" name="toggle_status" class="btn btn-sm <?= $a['status'] === 'Active' ? 'btn-outline-light' : 'btn-accent'; ?>">
                                        <?= $a['status'] === 'Active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted-custom">&mdash;</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
