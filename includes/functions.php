<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

function clean($conn, $value) {
    $value = trim($value);
    $value = strip_tags($value);
    $value = mysqli_real_escape_string($conn, $value);
    return $value;
}

function is_valid_email($email) {
    return preg_match('/^[\w.+-]+@[\w-]+\.[a-zA-Z]{2,}$/', $email);
}

function is_valid_contact($contact) {
    return preg_match('/^[0-9+\-\s]{7,20}$/', $contact);
}

function log_action($conn, $actor, $action) {
    $actor  = mysqli_real_escape_string($conn, $actor);
    $action = mysqli_real_escape_string($conn, $action);
    mysqli_query($conn, "INSERT INTO audit_log (actor, action) VALUES ('$actor', '$action')");
}

function require_customer_login() {
    if (!isset($_SESSION['customer_id'])) {
        header("Location: login.php");
        exit;
    }
}

function require_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
}

function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
}

function get_cart_items($conn) {
    $items = [];
    if (empty($_SESSION['cart'])) {
        return $items;
    }
    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $product_id = intval($product_id);
        $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $row['quantity'] = intval($qty);
            $row['line_total'] = $row['price'] * $row['quantity'];
            $items[] = $row;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    return $items;
}

function get_cart_total($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['line_total'];
    }
    return $total;
}

function get_cart_count() {
    if (empty($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}
