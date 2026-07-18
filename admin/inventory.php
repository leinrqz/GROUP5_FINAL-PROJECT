<?php
require_once __DIR__ . '/includes/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = clean($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = clean($conn, $_POST['image']);

    if ($name === '') $errors[] = "Item name is required.";
    if ($price <= 0) $errors[] = "Price must be greater than zero.";
    if ($stock < 0) $errors[] = "Quantity cannot be negative.";
    if ($image === '') $errors[] = "Please provide an image filename (from assets/img).";

    if (empty($errors)) {
        $sql = "INSERT INTO products (name, category_id, price, stock, image)
                VALUES ('$name', $category_id, $price, $stock, '$image')";
        if (mysqli_query($conn, $sql)) {
            log_action($conn, $_SESSION['admin_name'], "Added new product '$name' (stock: $stock, price: ₱$price).");
            $success = "Product '$name' added.";
        } else {
            $errors[] = "Could not add product.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    if ($price <= 0 || $stock < 0) {
        $errors[] = "Please provide a valid price and quantity.";
    } else {
        mysqli_query($conn, "UPDATE products SET price = $price, stock = $stock, category_id = $category_id WHERE id = $id");
        log_action($conn, $_SESSION['admin_name'], "Updated product #$id (price: ₱$price, stock: $stock).");
        $success = "Product #$id updated.";
    }
}

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) { $categories[] = $row; }

$products_result = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id");
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
    <h1 class="h2 fw-bold text-white">Apparel Stock Management</h1>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card p-4 mb-4">
    <h5 class="text-white mb-3">Add New Apparel Item</h5>
    <form method="POST" class="row g-3">
        <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Shirt Item Name" required></div>
        <div class="col-md-2">
            <select name="category_id" class="form-select">
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required></div>
        <div class="col-md-2"><input type="number" name="stock" class="form-control" placeholder="Quantity" required></div>
        <div class="col-md-2"><input type="text" name="image" class="form-control" placeholder="image file e.g. shirt1.jpg" required></div>
        <div class="col-md-1"><button type="submit" name="add_product" class="btn key-btn btn-success w-100">Add</button></div>
    </form>
    <p class="small text-muted-custom mt-2 mb-0">Image files must already exist inside <code>/assets/img/</code>.</p>
</div>

<div class="card p-4">
    <h5 class="text-white mb-3">Current Inventory</h5>
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead><tr><th>Item Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Update</th></tr></thead>
            <tbody>
                <?php while ($p = mysqli_fetch_assoc($products_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']); ?></td>
                        <td><?= htmlspecialchars($p['category_name']); ?></td>
                        <td class="price-tag">₱<?= number_format($p['price'], 2); ?></td>
                        <td><?= $p['stock'] <= 5 ? '<span class="status-pill status-inactive">' . $p['stock'] . ' left</span>' : $p['stock']; ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-2 align-items-center flex-wrap m-0">
                                <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                                <select name="category_id" class="form-select form-select-sm" style="width:110px;">
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id']; ?>" <?= $c['id'] == $p['category_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" step="0.01" name="price" value="<?= $p['price']; ?>" class="form-control form-control-sm" style="width:90px;">
                                <input type="number" name="stock" value="<?= $p['stock']; ?>" class="form-control form-control-sm" style="width:80px;">
                                <button type="submit" name="update_product" class="btn btn-sm btn-outline-light">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
