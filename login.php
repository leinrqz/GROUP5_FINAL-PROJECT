<?php
require_once __DIR__ . '/includes/functions.php';

if (is_customer_logged_in()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM customers WHERE email = '$email'");
    $user = $result ? mysqli_fetch_assoc($result) : null;

    if (!$user || !password_verify($password, $user['password'])) {
        $error = "Incorrect e-mail or password.";
    } elseif ($user['status'] !== 'Active') {
        $error = "Please confirm your e-mail before logging in. Check your inbox for the confirmation link.";
    } else {
        $_SESSION['customer_id'] = $user['id'];
        $_SESSION['customer_name'] = $user['full_name'];
        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-xl-4 col-lg-5 col-md-7">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-white">Welcome Back</h3>
                    <p class="text-muted-custom small">Log in to your CTRL+T account</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" class="form-control py-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-uppercase tracking-wider">Password</label>
                        <input type="password" name="password" class="form-control py-2" required>
                    </div>
                    <button type="submit" class="btn key-btn btn-primary w-100 py-2 fw-bold text-uppercase tracking-wider">Log In</button>
                    <p class="text-center small text-muted-custom mt-3 mb-0">No account yet? <a href="register.php">Register</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
