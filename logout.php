<?php
require_once __DIR__ . '/includes/functions.php';
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
header("Location: index.php");
exit;
