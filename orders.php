<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'My Orders';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get specific order if provided
$order_id = isset($_GET['order']) ? (int)$_GET['order'] : 0;

if ($order_id) {
    // Get specific order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        // Get order items
        $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image 
                              FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll();
    }
}

// Get all orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <h1>My Orders</h1>
    
    <?php display_flash(); ?>
    
    <?php if ($order_id && isset($order)): ?>
        <!-- Order Details -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="flex-between mb-3">
                    <h2>Order #<?php echo $order['id']; ?></h2>
                    <span class="badge badge-<?php echo get_status_badge($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                
                <div class="grid grid-2" style="margin-bottom: 2rem;">
                    <div>
                        <h3>Shipping Information</h3>
                        <p>
                            <strong>Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                            <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_zip']); ?>
                        </p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    </div>
                    <div>
                        <h3>Order Information</h3>
                        <p><strong>Order Date:</strong> <?php echo format_datetime($order['created_at']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        <p><strong>Total Amount:</strong> <span style="font-size: 1.5rem; color: #27ae60;"><?php echo format_price($order['total_amount']); ?></span></p>
                    </div>
                </div>
                
                <h3>Order Items</h3>
                <div class="table">
                    <table>
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
                                            <img src="<?php echo $item['image'] ? 'uploads/products/' . htmlspecialchars($item['image']) : 'assets/images/placeholder.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
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
                
                <div class="text-center mt-3">
                    <a href="orders.php" class="btn btn-secondary">Back to All Orders</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- All Orders -->
    <?php if (empty($orders)): ?>
        <div class="card">
            <div class="card-body text-center">
                <h2>No orders yet</h2>
                <p>Start shopping to place your first order</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        </div>
    <?php else: ?>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td><strong>#<?php echo $ord['id']; ?></strong></td>
                            <td><?php echo format_datetime($ord['created_at']); ?></td>
                            <td><strong><?php echo format_price($ord['total_amount']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php echo get_status_badge($ord['status']); ?>">
                                    <?php echo ucfirst($ord['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="orders.php?order=<?php echo $ord['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
