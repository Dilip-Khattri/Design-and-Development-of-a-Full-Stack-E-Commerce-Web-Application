<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/config.php';
}
require_once __DIR__ . '/functions.php';

$cart_count = 0;
if (is_logged_in()) {
    $cart_count = get_cart_count($pdo, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="cart.php" class="cart-icon">
                            Cart
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-count"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <?php if (is_admin()): ?>
                            <li><a href="admin/dashboard.php">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
