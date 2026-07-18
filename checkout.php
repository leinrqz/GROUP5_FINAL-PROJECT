<?php
require_once __DIR__ . '/includes/functions.php';
require_customer_login();

$cart_items = get_cart_items($conn);
$subtotal = get_cart_total($cart_items);
$errors = [];

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($conn, $_POST['full_name']);
    $address   = clean($conn, $_POST['address']);

    if ($full_name === '') $errors[] = "Full name is required.";
    if ($address === '') $errors[] = "Shipping address is required.";

    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock']) {
            $errors[] = $item['name'] . " only has " . $item['stock'] . " left in stock.";
        }
    }

    if (empty($errors)) {
        $customer_id = intval($_SESSION['customer_id']);
        $total = $subtotal;

        $sql = "INSERT INTO orders (customer_id, full_name, shipping_address, total, status)
                VALUES ($customer_id, '$full_name', '$address', $total, 'Placed')";
        mysqli_query($conn, $sql);
        $order_id = mysqli_insert_id($conn);

        foreach ($cart_items as $item) {
            $pid = intval($item['id']);
            $pname = mysqli_real_escape_string($conn, $item['name']);
            $price = floatval($item['price']);
            $qty = intval($item['quantity']);

            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                                  VALUES ($order_id, $pid, '$pname', $price, $qty)");

            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid");
        }

        log_action($conn, $_SESSION['customer_name'], "Placed order #$order_id (₱" . number_format($total, 2) . ")");

        $_SESSION['cart'] = [];
        header("Location: order-success.php?id=" . $order_id);
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="fw-bold text-white mb-4">Checkout</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card p-4">
            <h4 class="text-white mb-3 fw-bold">Shipping Information</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small text-uppercase tracking-wider">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_SESSION['customer_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-uppercase tracking-wider">Shipping Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <h4 class="text-white mt-4 mb-3 fw-bold">Payment Preview</h4>
                <div class="p-3 mb-3 rounded" style="background-color: #1b1e28; border: 1px solid #262a36;">
                    <p class="mb-1 text-white fw-medium">Simulated Payment Gateway</p>
                    <small class="text-muted-custom">No live banking APIs are attached to this sequence environment, as required by the project brief.</small>
                </div>
                <button type="submit" class="btn key-btn btn-success w-100 py-2 fw-bold">PLACE ORDER</button>
            </form>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card p-4">
            <h4 class="text-white mb-3 fw-bold">Items Review</h4>
            <?php foreach ($cart_items as $item): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted-custom"><?= htmlspecialchars($item['name']); ?> (x<?= $item['quantity']; ?>)</span>
                    <span class="price-tag">₱<?= number_format($item['line_total'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <hr class="border-secondary">
            <div class="d-flex justify-content-between">
                <span class="fw-bold text-white">Total</span>
                <span class="fw-bold price-tag fs-5">₱<?= number_format($subtotal, 2); ?></span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
