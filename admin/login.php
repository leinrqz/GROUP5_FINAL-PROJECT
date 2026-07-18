<?php
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username'");
    $admin = $result ? mysqli_fetch_assoc($result) : null;

    if (!$admin || !password_verify($password, $admin['password'])) {
        $error = "Incorrect username or password.";
    } elseif ($admin['status'] !== 'Active') {
        $error = "This admin account has been deactivated.";
    } else {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];
        log_action($conn, $admin['full_name'], "Logged into the admin console.");
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRL+T Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-xl-4 col-lg-5 col-md-7">
            <div class="text-center mb-4">
                <span class="brand-keys">
                    <span class="keycap">Ctrl</span>
                    <span class="keycap keycap-accent">+</span>
                    <span class="keycap">T</span>
                </span>
                <div class="text-white fw-bold mt-2">ADMIN CONSOLE</div>
            </div>
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase tracking-wider">Username</label>
                            <input type="text" name="username" class="form-control py-2" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-uppercase tracking-wider">Password</label>
                            <input type="password" name="password" class="form-control py-2" required>
                        </div>
                        <button type="submit" class="btn key-btn btn-primary w-100 py-2 fw-bold text-uppercase tracking-wider">Enter Console</button>
                    </form>
                </div>
            </div>
            <p class="text-center small text-muted-custom mt-3"><a href="../index.php">&larr; Back to store</a></p>
        </div>
    </div>
</div>
</body>
</html>
