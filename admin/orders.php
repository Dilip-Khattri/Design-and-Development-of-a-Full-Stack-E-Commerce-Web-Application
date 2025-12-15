<?php
$page_title = 'Orders Management';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

// Handle status update
if (isset($_POST['action']) && $_POST['action'] === 'update_status' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    
    $valid_statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        set_flash('success', 'Order status updated successfully');
        redirect('orders.php');
    }
}

// Get specific order details if ID is provided
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order_details = null;
$order_items = [];

if ($order_id) {
    $stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.id 
                          WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetch();
    
    if ($order_details) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image 
                              FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll();
    }
}

// Get all orders
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$where = $status_filter ? "WHERE o.status = ?" : "";
$params = $status_filter ? [$status_filter] : [];

$stmt = $pdo->prepare("SELECT o.*, u.name as user_name 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       $where 
                       ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1>Orders Management</h1>
    <div class="breadcrumb">Admin / Orders</div>
</div>

<?php display_flash(); ?>

<?php if ($order_details): ?>
    <!-- Order Details -->
    <div class="admin-table-container" style="margin-bottom: 2rem;">
        <div class="table-header">
            <h2>Order #<?php echo $order_details['id']; ?> Details</h2>
            <a href="orders.php" class="btn btn-secondary">Back to All Orders</a>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 1.5rem;">
            <div>
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['user_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></p>
                
                <h3 style="margin-top: 2rem;">Shipping Address</h3>
                <p>
                    <?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?><br>
                    <?php echo htmlspecialchars($order_details['shipping_city']); ?>, <?php echo htmlspecialchars($order_details['shipping_zip']); ?>
                </p>
            </div>
            
            <div>
                <h3>Order Information</h3>
                <p><strong>Order Date:</strong> <?php echo format_datetime($order_details['created_at']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order_details['payment_method'])); ?></p>
                <p><strong>Total Amount:</strong> <span style="font-size: 1.5rem; color: #27ae60;"><?php echo format_price($order_details['total_amount']); ?></span></p>
                
                <h3 style="margin-top: 2rem;">Order Status</h3>
                <form method="POST" action="">
                    <?php echo csrf_token_field(); ?>
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <select name="status" style="flex: 1;">
                            <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $order_details['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $order_details['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order_details['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
        
        <h3 style="padding: 0 1.5rem;">Order Items</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="<?php echo $item['image'] ? '../uploads/products/' . htmlspecialchars($item['image']) : '../assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                            </div>
                        </td>
                        <td><?php echo format_price($item['price']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- All Orders -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>All Orders (<?php echo count($orders); ?>)</h2>
    </div>
    
    <!-- Status Filter -->
    <div style="margin-bottom: 1.5rem;">
        <a href="orders.php" class="btn btn-sm <?php echo !$status_filter ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="orders.php?status=pending" class="btn btn-sm <?php echo $status_filter == 'pending' ? 'btn-warning' : 'btn-secondary'; ?>">Pending</a>
        <a href="orders.php?status=paid" class="btn btn-sm <?php echo $status_filter == 'paid' ? 'btn-info' : 'btn-secondary'; ?>">Paid</a>
        <a href="orders.php?status=shipped" class="btn btn-sm <?php echo $status_filter == 'shipped' ? 'btn-primary' : 'btn-secondary'; ?>">Shipped</a>
        <a href="orders.php?status=delivered" class="btn btn-sm <?php echo $status_filter == 'delivered' ? 'btn-success' : 'btn-secondary'; ?>">Delivered</a>
        <a href="orders.php?status=cancelled" class="btn btn-sm <?php echo $status_filter == 'cancelled' ? 'btn-danger' : 'btn-secondary'; ?>">Cancelled</a>
    </div>
    
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
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

<?php require_once 'includes/admin-footer.php'; ?>
