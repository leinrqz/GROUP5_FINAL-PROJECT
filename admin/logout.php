<?php
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    log_action($conn, $_SESSION['admin_name'], "Logged out of the admin console.");
}

unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);
header("Location: login.php");
exit;
