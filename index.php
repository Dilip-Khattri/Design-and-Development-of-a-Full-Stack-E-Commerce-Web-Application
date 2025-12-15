<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Get featured products
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.featured = 1 
                       ORDER BY p.created_at DESC 
                       LIMIT 6");
$stmt->execute();
$featured_products = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<!-- Hero Section -->
<div class="hero">
    <div class="container">
        <h1>Welcome to <?php echo htmlspecialchars(get_setting($pdo, 'site_name', SITE_NAME)); ?></h1>
        <p>Discover amazing products at great prices</p>
        <a href="products.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Shop Now</a>
    </div>
</div>

<div class="container">
    <!-- Categories -->
    <section style="margin: 3rem 0;">
        <h2 class="text-center mb-3">Shop by Category</h2>
        <div class="grid grid-3">
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['slug']; ?>" class="card" style="text-decoration: none; color: inherit;">
                    <div class="card-body text-center">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Featured Products -->
    <section style="margin: 3rem 0;">
        <h2 class="text-center mb-3">Featured Products</h2>
        
        <?php if (empty($featured_products)): ?>
            <p class="text-center">No featured products available.</p>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($featured_products as $product): ?>
                    <div class="card">
                        <img src="<?php echo $product['image'] ? 'uploads/products/' . htmlspecialchars($product['image']) : 'assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="card-img">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="card-text"><?php echo truncate($product['description'], 100); ?></p>
                            <div class="card-price"><?php echo format_price($product['price']); ?></div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="flex: 1;">View Details</a>
                                <?php if (is_logged_in()): ?>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success">Add to Cart</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-secondary">View All Products</a>
        </div>
    </section>
    
    <!-- Features -->
    <section style="margin: 3rem 0;">
        <div class="grid grid-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3>🚚 Free Shipping</h3>
                    <p>On orders over $50</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <h3>🔒 Secure Payment</h3>
                    <p>100% secure transactions</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <h3>↩️ Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
