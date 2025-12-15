<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Shopping Cart';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image, p.stock 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = $subtotal > 50 ? 0 : 10; // Free shipping over $50
$total = $subtotal + $shipping;

include 'includes/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <h2>Your cart is empty</h2>
                <p>Start shopping to add items to your cart</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        </div>
    <?php else: ?>
        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="<?php echo $item['image'] ? 'uploads/products/' . htmlspecialchars($item['image']) : 'assets/images/placeholder.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="cart-item-image">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        <?php if ($item['quantity'] > $item['stock']): ?>
                                            <br><span style="color: #e74c3c; font-size: 0.9rem;">Only <?php echo $item['stock']; ?> in stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo format_price($item['price']); ?></td>
                            <td>
                                <div class="quantity-control">
                                    <button class="btn btn-sm btn-secondary qty-decrement">-</button>
                                    <input type="number" class="quantity-input" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['stock']; ?>" 
                                           data-cart-id="<?php echo $item['id']; ?>">
                                    <button class="btn btn-sm btn-secondary qty-increment">+</button>
                                </div>
                            </td>
                            <td><strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong></td>
                            <td>
                                <button onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                        class="btn btn-danger btn-sm">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            
            <div class="summary-row">
                <span>Subtotal:</span>
                <span><?php echo format_price($subtotal); ?></span>
            </div>
            
            <div class="summary-row">
                <span>Shipping:</span>
                <span><?php echo $shipping > 0 ? format_price($shipping) : 'FREE'; ?></span>
            </div>
            
            <?php if ($shipping > 0): ?>
                <p style="font-size: 0.9rem; color: #666; margin: 0.5rem 0;">
                    Add <?php echo format_price(50 - $subtotal); ?> more for free shipping
                </p>
            <?php endif; ?>
            
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span><?php echo format_price($total); ?></span>
            </div>
            
            <a href="checkout.php" class="btn btn-primary btn-block mt-3">Proceed to Checkout</a>
            <a href="products.php" class="btn btn-secondary btn-block mt-2">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php echo csrf_token_field(); ?>

<?php include 'includes/footer.php'; ?>
