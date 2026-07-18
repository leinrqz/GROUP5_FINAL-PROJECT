<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = clean($conn, $_POST['fullname']);
    $email    = clean($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address  = clean($conn, $_POST['address']);
    $contact  = clean($conn, $_POST['contact']);

    if ($fullname === '') $errors[] = "Complete name is required.";
    if (!is_valid_email($email)) $errors[] = "Please enter a valid e-mail address.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm_password) $errors[] = "Password and confirm password do not match.";
    if ($address === '') $errors[] = "Complete delivery address is required.";
    if (!is_valid_contact($contact)) $errors[] = "Please enter a valid contact number.";

    if (empty($errors)) {
        $dupe = mysqli_query($conn, "SELECT id FROM customers WHERE email = '$email'");
        if ($dupe && mysqli_num_rows($dupe) > 0) {
            $errors[] = "An account with that e-mail already exists.";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $sql = "INSERT INTO customers (full_name, email, password, address, contact_number, confirm_token, status)
                VALUES ('$fullname', '$email', '$hashed_password', '$address', '$contact', '$token', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            $base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $confirm_link = rtrim($base_url, '/') . "/confirm.php?email=" . urlencode($email) . "&token=" . $token;

            $subject = "Confirm your CTRL+T account";
            $message = "Hi $fullname,\n\nThanks for registering with CTRL+T. Please confirm your e-mail by clicking the link below:\n\n$confirm_link\n\nIf you did not create this account, you can ignore this e-mail.";
            $headers = "From: no-reply@ctrlt.test";

            @mail($email, $subject, $message, $headers);

            $success = true;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-xl-5 col-lg-6 col-md-8">
        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-white">Create Account</h3>
                    <p class="text-muted-custom small">Join CTRL+T membership for exclusive drop releases</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Almost there! We sent a confirmation link to your e-mail. Please open it to activate your account before logging in.
                    </div>
                    <a href="login.php" class="btn key-btn btn-primary w-100">Go to Login</a>
                <?php else: ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase tracking-wider">Complete Name</label>
                            <input type="text" name="fullname" class="form-control py-2" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase tracking-wider">Email Address</label>
                            <input type="email" name="email" class="form-control py-2" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small text-uppercase tracking-wider">Password</label>
                                <input type="password" name="password" class="form-control py-2" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small text-uppercase tracking-wider">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control py-2" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase tracking-wider">Complete Delivery Address</label>
                            <textarea name="address" class="form-control" rows="2" required><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-uppercase tracking-wider">Contact Number</label>
                            <input type="text" name="contact" class="form-control py-2" value="<?= isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>" required>
                        </div>
                        <button type="submit" class="btn key-btn btn-primary w-100 py-2 fw-bold text-uppercase tracking-wider">Register Account</button>
                        <p class="text-center small text-muted-custom mt-3 mb-0">Already have an account? <a href="login.php">Log in</a></p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
