<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Require login
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'add':
            $product_id = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if (!$product_id || $quantity < 1) {
                echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
                exit;
            }
            
            // Check if product exists and has stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $cart_item = $stmt->fetch();
            
            if ($cart_item) {
                // Update quantity
                $new_quantity = $cart_item['quantity'] + $quantity;
                
                if ($new_quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $cart_item['id']]);
            } else {
                // Add new item
                if ($quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $product_id, $quantity]);
            }
            
            $cart_count = get_cart_count($pdo, $user_id);
            echo json_encode(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $cart_count]);
            break;
            
        case 'update':
            $cart_id = (int)($_POST['cart_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if (!$cart_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
                exit;
            }
            
            if ($quantity < 1) {
                // Remove item
                $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            } else {
                // Check stock
                $stmt = $pdo->prepare("SELECT p.stock FROM cart c 
                                      JOIN products p ON c.product_id = p.id 
                                      WHERE c.id = ? AND c.user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                $item = $stmt->fetch();
                
                if (!$item) {
                    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
                    exit;
                }
                
                if ($quantity > $item['stock']) {
                    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$quantity, $cart_id, $user_id]);
                echo json_encode(['success' => true, 'message' => 'Cart updated']);
            }
            break;
            
        case 'remove':
            $cart_id = (int)($_POST['cart_id'] ?? 0);
            
            if (!$cart_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
