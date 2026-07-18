<?php
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_to_cart'])) {
        $pid = intval($_POST['product_id']);
        $check = mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $pid");
        if ($check && $product = mysqli_fetch_assoc($check)) {
            $current_qty = $_SESSION['cart'][$pid] ?? 0;
            if ($current_qty + 1 <= $product['stock']) {
                $_SESSION['cart'][$pid] = $current_qty + 1;
            }
        }
        header("Location: index.php?added=1");
        exit;
    }

    if (isset($_POST['update_quantity'])) {
        $pid = intval($_POST['product_id']);
        $qty = intval($_POST['quantity']);

        $check = mysqli_query($conn, "SELECT stock FROM products WHERE id = $pid");
        $product = $check ? mysqli_fetch_assoc($check) : null;

        if ($qty <= 0 || !$product) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid] = min($qty, $product['stock']);
        }
        header("Location: cart.php");
        exit;
    }

    if (isset($_POST['remove_item'])) {
        $pid = intval($_POST['product_id']);
        unset($_SESSION['cart'][$pid]);
        header("Location: cart.php");
        exit;
    }
}

$cart_items = get_cart_items($conn);
$subtotal = get_cart_total($cart_items);

include __DIR__ . '/includes/header.php';
?>

<h2 class="fw-bold text-white mb-4">Your Shopping Cart</h2>

<div class="row g-4">
    <div class="col-lg-8">
        <?php if (empty($cart_items)): ?>
            <div class="card p-5 text-center">
                <p class="text-muted-custom mb-3">Your shopping cart is currently empty.</p>
                <a href="index.php" class="btn key-btn btn-primary btn-sm px-4 align-self-center">Return to Store</a>
            </div>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="card p-4 mb-3">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="assets/img/<?= htmlspecialchars($item['image']); ?>" class="rounded" alt="<?= htmlspecialchars($item['name']); ?>" style="width: 70px; height: 70px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0 text-white fw-bold"><?= htmlspecialchars($item['name']); ?></h5>
                                <small class="text-muted-custom">Stock left: <?= $item['stock']; ?></small>
                                <div class="price-tag mt-1">₱<?= number_format($item['price'], 2); ?> each</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 justify-content-between justify-content-sm-end">
                            <form action="cart.php" method="POST" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="product_id" value="<?= $item['id']; ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity']; ?>" class="form-control text-center p-1" style="width: 60px;" min="1" max="<?= $item['stock']; ?>">
                                <button type="submit" name="update_quantity" class="btn btn-outline-light btn-sm">Update</button>
                            </form>

                            <form action="cart.php" method="POST" class="m-0">
                                <input type="hidden" name="product_id" value="<?= $item['id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-link text-danger p-0 text-decoration-none small">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 position-sticky" style="top: 20px;">
            <h4 class="fw-bold text-white mb-3">Order Summary</h4>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted-custom">Subtotal</span>
                <span class="price-tag">₱<?= number_format($subtotal, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted-custom">Shipping</span>
                <span class="text-success"><?= $subtotal > 0 ? 'Calculated next' : '$0.00'; ?></span>
            </div>
            <hr class="border-secondary">
            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold text-white">Estimated Total</span>
                <span class="fw-bold price-tag fs-5">₱<?= number_format($subtotal, 2); ?></span>
            </div>
            <?php if ($subtotal > 0): ?>
                <a href="checkout.php" class="btn key-btn btn-success w-100 py-2 fw-bold text-uppercase tracking-wider">Proceed to Checkout</a>
            <?php else: ?>
                <button class="btn btn-secondary w-100 py-2 fw-bold text-uppercase tracking-wider" disabled>Cart is Empty</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
