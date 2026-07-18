<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin_login();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRL+T Admin Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3">
            <div class="mb-4 px-2 d-flex align-items-center gap-2">
                <span class="brand-keys">
                    <span class="keycap">Ctrl</span>
                    <span class="keycap keycap-accent">+</span>
                    <span class="keycap">T</span>
                </span>
                <span class="text-white fw-bold">ADMIN</span>
            </div>
            <ul class="nav flex-column gap-1">
                <li class="nav-item"><a class="nav-link rounded p-2 <?= $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">Dashboard Users</a></li>
                <li class="nav-item"><a class="nav-link rounded p-2 <?= $current_page === 'inventory.php' ? 'active' : ''; ?>" href="inventory.php">Manage Stock</a></li>
                <li class="nav-item"><a class="nav-link rounded p-2 <?= $current_page === 'reports.php' ? 'active' : ''; ?>" href="reports.php">System Reports</a></li>
                <li class="nav-item mt-4 px-2 small text-muted-custom">Logged in as<br><span class="text-white fw-semibold"><?= htmlspecialchars($_SESSION['admin_name']); ?></span></li>
                <li class="nav-item mt-2"><a class="nav-link text-danger small p-2" href="logout.php">Log out of console</a></li>
                <li class="nav-item"><a class="nav-link text-muted-custom small p-2" href="../index.php">Exit to Store</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
