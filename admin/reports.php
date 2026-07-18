<?php
require_once __DIR__ . '/includes/header.php';

$inventory_result = mysqli_query($conn, "SELECT p.name, c.name AS category_name, p.stock
    FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.stock ASC");

$audit_result = mysqli_query($conn, "SELECT * FROM audit_log ORDER BY id DESC LIMIT 100");
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
    <h1 class="h2 fw-bold text-white">System Reports Hub</h1>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="text-white mb-3">Inventory Balance Sheet</h4>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle">
                    <thead><tr><th>Item Name</th><th>Category</th><th>Rem. Stock</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($inventory_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['category_name']); ?></td>
                                <td><?= $row['stock']; ?> units</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="text-white mb-3">Administrative Audit Log</h4>
            <p class="small text-muted-custom">Every login and every add/edit action performed by an admin is recorded here, most recent first.</p>
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-dark table-striped align-middle">
                    <thead><tr><th>Timestamp</th><th>Operator</th><th>Action Event</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($audit_result)): ?>
                            <tr>
                                <td class="small font-monospace"><?= htmlspecialchars($row['created_at']); ?></td>
                                <td><?= htmlspecialchars($row['actor']); ?></td>
                                <td><?= htmlspecialchars($row['action']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
