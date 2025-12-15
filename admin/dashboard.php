<?php
$page_title = 'Dashboard';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

// Get statistics
$stats = [];

// Total products
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$stats['products'] = $stmt->fetchColumn();

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$stats['orders'] = $stmt->fetchColumn();

// Total revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN ('paid', 'shipped', 'delivered')");
$stats['revenue'] = $stmt->fetchColumn();

// Total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
$stats['users'] = $stmt->fetchColumn();

// Recent orders
$stmt = $pdo->query("SELECT o.*, u.name as user_name 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC 
                     LIMIT 10");
$recent_orders = $stmt->fetchAll();

// Low stock products
$stmt = $pdo->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
$low_stock = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1>Dashboard</h1>
    <div class="breadcrumb">Admin / Dashboard</div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <h3>Total Products</h3>
        <div class="stat-value"><?php echo $stats['products']; ?></div>
    </div>
    
    <div class="stat-card success">
        <h3>Total Orders</h3>
        <div class="stat-value"><?php echo $stats['orders']; ?></div>
    </div>
    
    <div class="stat-card warning">
        <h3>Total Revenue</h3>
        <div class="stat-value"><?php echo format_price($stats['revenue']); ?></div>
    </div>
    
    <div class="stat-card danger">
        <h3>Total Users</h3>
        <div class="stat-value"><?php echo $stats['users']; ?></div>
    </div>
</div>

<!-- Recent Orders -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>Recent Orders</h2>
        <a href="orders.php" class="btn btn-primary">View All</a>
    </div>
    
    <?php if (empty($recent_orders)): ?>
        <p>No orders yet</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><strong><?php echo format_price($order['total_amount']); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo get_status_badge($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo format_datetime($order['created_at']); ?></td>
                        <td>
                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Low Stock Alert -->
<?php if (!empty($low_stock)): ?>
    <div class="admin-table-container" style="margin-top: 2rem;">
        <div class="table-header">
            <h2>Low Stock Alert</h2>
            <a href="products.php" class="btn btn-warning">Manage Products</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($low_stock as $product): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="<?php echo $product['image'] ? '../uploads/products/' . htmlspecialchars($product['image']) : '../assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                            </div>
                        </td>
                        <td>
                            <span style="color: <?php echo $product['stock'] == 0 ? '#e74c3c' : '#f39c12'; ?>; font-weight: bold;">
                                <?php echo $product['stock']; ?>
                            </span>
                        </td>
                        <td><?php echo format_price($product['price']); ?></td>
                        <td>
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>
