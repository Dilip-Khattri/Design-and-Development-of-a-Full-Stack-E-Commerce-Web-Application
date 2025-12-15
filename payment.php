<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Payment';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout.php');
}

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid request';
    redirect('checkout.php');
}

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.stock 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Validate cart is not empty
if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty';
    redirect('cart.php');
}

// Validate shipping information
$required_fields = ['name', 'email', 'phone', 'address', 'city', 'zip'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'All shipping fields are required';
        redirect('checkout.php');
    }
}

$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$address = sanitize($_POST['address']);
$city = sanitize($_POST['city']);
$zip = sanitize($_POST['zip']);

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = $subtotal > 50 ? 0 : 10;
$total = $subtotal + $shipping;

include 'includes/header.php';
?>

<div class="container" style="max-width: 600px; margin: 3rem auto;">
    <div class="card">
        <div class="card-body">
            <h1 class="text-center">Dummy Payment</h1>
            
            <div class="alert alert-info">
                <strong>This is a demo payment system.</strong><br>
                No real payment will be processed. Click "Complete Payment" to simulate a successful payment.
            </div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 5px; margin: 2rem 0;">
                <h3>Order Total: <?php echo format_price($total); ?></h3>
                <p style="margin: 0.5rem 0;">Items: <?php echo count($cart_items); ?></p>
            </div>
            
            <form method="POST" action="process-payment.php">
                <?php echo csrf_token_field(); ?>
                
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                <input type="hidden" name="zip" value="<?php echo htmlspecialchars($zip); ?>">
                <input type="hidden" name="total" value="<?php echo $total; ?>">
                
                <div class="form-group">
                    <label>Simulated Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="credit_card">Credit Card (Dummy)</option>
                        <option value="debit_card">Debit Card (Dummy)</option>
                        <option value="paypal">PayPal (Dummy)</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success btn-block">Complete Payment</button>
                    <a href="checkout.php" class="btn btn-secondary btn-block">Go Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
