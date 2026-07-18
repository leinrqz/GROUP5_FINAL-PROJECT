<?php
require_once __DIR__ . '/includes/functions.php';
require_customer_login();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$order_result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id AND customer_id = " . intval($_SESSION['customer_id']));
$order = $order_result ? mysqli_fetch_assoc($order_result) : null;

if (!$order) {
    header("Location: index.php");
    exit;
}

$items_result = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card p-5 text-center mb-4">
            <h2 class="fw-bold text-white mb-2">Order Placed!</h2>
            <p class="text-muted-custom">Thanks, <?= htmlspecialchars($order['full_name']); ?>. Your order <span class="price-tag">#<?= $order['id']; ?></span> has been received.</p>
        </div>
        <div class="card p-4">
            <h5 class="text-white mb-3 fw-bold">Order Summary</h5>
            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted-custom"><?= htmlspecialchars($item['product_name']); ?> (x<?= $item['quantity']; ?>)</span>
                    <span class="price-tag">₱<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endwhile; ?>
            <hr class="border-secondary">
            <div class="d-flex justify-content-between mb-1">
                <span class="fw-bold text-white">Total</span>
                <span class="fw-bold price-tag fs-5">₱<?= number_format($order['total'], 2); ?></span>
            </div>
            <p class="small text-muted-custom mt-3 mb-0">Shipping to: <?= htmlspecialchars($order['shipping_address']); ?></p>
        </div>
        <a href="index.php" class="btn key-btn btn-primary mt-4">Continue Shopping</a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
