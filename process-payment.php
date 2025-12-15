<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

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

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get cart items
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.stock 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ? FOR UPDATE");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }
    
    // Validate stock and calculate total
    $subtotal = 0;
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception($item['name'] . ' is out of stock');
        }
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    // Calculate totals with tax and delivery using dynamic settings
    $order_totals = calculate_order_total($pdo, $subtotal);
    $total = $order_totals['total'];
    
    // Get form data
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $zip = sanitize($_POST['zip']);
    $phone = sanitize($_POST['phone']);
    $payment_method = sanitize($_POST['payment_method'] ?? 'dummy');
    
    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, shipping_city, shipping_zip, phone, status, payment_method) 
                          VALUES (?, ?, ?, ?, ?, ?, 'paid', ?)");
    $stmt->execute([$user_id, $total, $address, $city, $zip, $phone, $payment_method]);
    $order_id = $pdo->lastInsertId();
    
    // Create order items and update stock
    foreach ($cart_items as $item) {
        // Add order item
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        
        // Update product stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Set success message
    $_SESSION['success'] = 'Order placed successfully! Order ID: #' . $order_id;
    redirect('orders.php?order=' . $order_id);
    
} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    $_SESSION['error'] = 'Payment failed: ' . $e->getMessage();
    redirect('checkout.php');
}
?>
