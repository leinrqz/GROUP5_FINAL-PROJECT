<?php
require_once __DIR__ . '/includes/functions.php';

$state = 'error';
$message = 'This confirmation link is invalid.';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = clean($conn, $_GET['email']);
    $token = clean($conn, $_GET['token']);

    $result = mysqli_query($conn, "SELECT id, status FROM customers WHERE email = '$email' AND confirm_token = '$token'");

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if ($row['status'] === 'Active') {
            $state = 'already';
            $message = 'This account is already confirmed. You can log in now.';
        } else {
            $id = intval($row['id']);
            mysqli_query($conn, "UPDATE customers SET status = 'Active' WHERE id = $id");
            $state = 'success';
            $message = 'Your e-mail has been confirmed. You can now log in.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card p-5">
            <h3 class="fw-bold text-white mb-3">Account Confirmation</h3>
            <div class="alert <?= $state === 'error' ? 'alert-danger' : 'alert-success'; ?>"><?= htmlspecialchars($message); ?></div>
            <a href="login.php" class="btn key-btn btn-primary align-self-center px-4">Go to Login</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
