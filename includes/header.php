<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRL+T | Premium Shirts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-keys">
                <span class="keycap">Ctrl</span>
                <span class="keycap keycap-accent">+</span>
                <span class="keycap">T</span>
            </span>
            <span>CTRL+T</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link px-3" href="index.php">Store</a></li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="cart.php">
                        Cart
                        <?php $c = get_cart_count(); if ($c > 0): ?>
                            <span class="cart-badge"><?= $c; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link px-3" href="about.php">About</a></li>
                <?php if (is_customer_logged_in()): ?>
                    <li class="nav-item"><span class="nav-link px-3 text-muted-custom">Hi, <?= htmlspecialchars($_SESSION['customer_name']); ?></span></li>
                    <li class="nav-item"><a class="nav-link px-3" href="logout.php">Log out</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link px-3" href="login.php">Log in</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="register.php">Register</a></li>
                <?php endif; ?>
                <li class="nav-item ms-lg-2"><a class="btn key-btn btn-outline-light btn-sm px-3" href="admin/login.php">Portal</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
