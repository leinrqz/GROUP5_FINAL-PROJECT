<?php
require_once __DIR__ . '/includes/functions.php';

$cat_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY id");
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row;
}

$current_category = isset($_GET['category']) ? $_GET['category'] : 'All';
$valid_names = array_column($categories, 'name');

if ($current_category !== 'All' && !in_array($current_category, $valid_names, true)) {
    $current_category = 'All';
}

if ($current_category === 'All') {
    $sql = "SELECT p.*, c.name AS category_name FROM products p
            JOIN categories c ON p.category_id = c.id ORDER BY p.id";
    $result = mysqli_query($conn, $sql);
} else {
    $safe_cat = clean($conn, $current_category);
    $sql = "SELECT p.*, c.name AS category_name FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE c.name = '$safe_cat' ORDER BY p.id";
    $result = mysqli_query($conn, $sql);
}

$shirts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $shirts[] = $row;
}

include __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <div class="row align-items-center">
        <div class="col-md-7">
            <div class="hero-eyebrow mb-2"></div>
            <h1 class="display-5 fw-bold text-white mb-2">CTRL+T Clothing Line</h1>
            <p class="text-muted-custom lead mb-0">Premium structural fit shirts built for modern lifespans.</p>
        </div>
        <div class="col-md-5 text-md-end mt-4 mt-md-0">
            <div class="btn-group flex-wrap">
                <a href="index.php?category=All" class="btn btn-outline-light btn-sm px-3 <?= $current_category === 'All' ? 'active' : ''; ?>">All Shirts</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?= urlencode($cat['name']); ?>"
                       class="btn btn-outline-light btn-sm px-3 <?= $current_category === $cat['name'] ? 'active' : ''; ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Added to cart.</div>
<?php endif; ?>

<div class="row">
    <?php if (empty($shirts)): ?>
        <div class="col-12 text-center my-5">
            <p class="text-muted-custom">No shirts available in this category at the moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($shirts as $shirt): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card product-card h-100 shadow-sm overflow-hidden">
                    <img src="assets/img/<?= htmlspecialchars($shirt['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($shirt['name']); ?>">
                    <div class="card-body d-flex flex-column p-4">
                        <span class="badge bg-secondary mb-2 align-self-start py-2 px-3"><?= htmlspecialchars($shirt['category_name']); ?></span>
                        <h5 class="card-title fw-bold text-white mb-1"><?= htmlspecialchars($shirt['name']); ?></h5>
                        <p class="card-text price-tag mb-1" style="font-size: 1.15rem;">₱<?= number_format($shirt['price'], 2); ?></p>
                        <p class="small text-muted-custom mb-3"><?= $shirt['stock'] > 0 ? $shirt['stock'] . ' in stock' : 'Out of stock'; ?></p>

                        <form action="cart.php" method="POST" class="mt-auto">
                            <input type="hidden" name="product_id" value="<?= $shirt['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn key-btn btn-primary w-100 py-2 fw-medium" <?= $shirt['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <?= $shirt['stock'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
