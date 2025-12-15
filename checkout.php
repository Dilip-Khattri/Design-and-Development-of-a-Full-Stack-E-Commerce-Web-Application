<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Checkout';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.stock 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Redirect if cart is empty
if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty';
    redirect('cart.php');
}

// Check stock availability
$stock_issues = [];
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock']) {
        $stock_issues[] = $item['name'] . ' (only ' . $item['stock'] . ' available)';
    }
}

if (!empty($stock_issues)) {
    $_SESSION['error'] = 'Some items in your cart are out of stock: ' . implode(', ', $stock_issues);
    redirect('cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Calculate totals with tax and delivery using dynamic settings
$order_totals = calculate_order_total($pdo, $subtotal);
$tax = $order_totals['tax'];
$shipping = $order_totals['shipping'];
$total = $order_totals['total'];

// Get user details
$user = get_current_user($pdo);

include 'includes/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <h1>Checkout</h1>
    
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Shipping Information</h2>
            
            <form method="POST" action="payment.php" id="checkout-form">
                <?php echo csrf_token_field(); ?>
                
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Street Address *</label>
                    <textarea id="address" name="address" required rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="zip">ZIP Code *</label>
                        <input type="text" id="zip" name="zip" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Order Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Any special instructions for delivery"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Proceed to Payment</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <br>
                        <small>Qty: <?php echo $item['quantity']; ?> × <?php echo format_price($item['price']); ?></small>
                    </div>
                    <div>
                        <strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="border-top: 2px solid #eee; margin-top: 1rem; padding-top: 1rem;">
                <div class="order-item" style="border: none;">
                    <span>Subtotal:</span>
                    <span><?php echo format_price($subtotal); ?></span>
                </div>
                
                <?php if ($tax > 0): ?>
                <div class="order-item" style="border: none;">
                    <span>Tax (<?php echo $order_totals['tax_rate']; ?>%):</span>
                    <span><?php echo format_price($tax); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="order-item" style="border: none;">
                    <span>Shipping:</span>
                    <span><?php echo $shipping > 0 ? format_price($shipping) : 'FREE'; ?></span>
                </div>
                
                <div class="order-item" style="border: none; font-size: 1.25rem; font-weight: bold;">
                    <span>Total:</span>
                    <span><?php echo format_price($total); ?></span>
                </div>
            </div>
            
            <div class="alert alert-info" style="margin-top: 1rem;">
                <strong>Note:</strong> This is a demo payment. No real charges will be made.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
