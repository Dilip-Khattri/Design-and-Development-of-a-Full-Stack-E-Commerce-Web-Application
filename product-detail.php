<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    redirect('products.php');
}

// Get product details
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    redirect('products.php');
}

$page_title = $product['name'];

// Get related products from same category
$stmt = $pdo->prepare("SELECT * FROM products 
                       WHERE category_id = ? AND id != ? 
                       ORDER BY RAND() 
                       LIMIT 4");
$stmt->execute([$product['category_id'], $product_id]);
$related_products = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 2rem; color: #666;">
        <a href="index.php">Home</a> &gt; 
        <a href="products.php">Products</a> &gt; 
        <?php if ($product['category_name']): ?>
            <a href="products.php?category=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a> &gt; 
        <?php endif; ?>
        <?php echo htmlspecialchars($product['name']); ?>
    </div>
    
    <!-- Product Detail -->
    <div class="product-detail">
        <div class="product-image">
            <img src="<?php echo $product['image'] ? 'uploads/products/' . htmlspecialchars($product['image']) : 'assets/images/placeholder.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-meta">
                <?php if ($product['category_name']): ?>
                    <span>Category: <a href="products.php?category=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></span>
                <?php endif; ?>
                <span class="<?php echo $product['stock'] > 0 ? 'stock-status' : 'out-of-stock'; ?>">
                    <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock'; ?>
                </span>
            </div>
            
            <div class="card-price" style="font-size: 2rem; margin: 1.5rem 0;">
                <?php echo format_price($product['price']); ?>
            </div>
            
            <div style="margin: 1.5rem 0;">
                <h3>Description</h3>
                <p style="line-height: 1.8;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <?php if (is_logged_in()): ?>
                <?php if ($product['stock'] > 0): ?>
                    <div style="display: flex; gap: 1rem; align-items: center; margin: 2rem 0;">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                               style="width: 80px; padding: 0.5rem;">
                        <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)" 
                                class="btn btn-success" style="font-size: 1.1rem; padding: 0.8rem 2rem;">
                            Add to Cart
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        This product is currently out of stock
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    Please <a href="login.php">login</a> to add items to cart
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <section style="margin: 4rem 0;">
            <h2>Related Products</h2>
            <div class="grid grid-4 mt-3">
                <?php foreach ($related_products as $related): ?>
                    <div class="card">
                        <img src="<?php echo $related['image'] ? 'uploads/products/' . htmlspecialchars($related['image']) : 'assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>" 
                             class="card-img">
                        <div class="card-body">
                            <h3 class="card-title" style="font-size: 1rem;"><?php echo htmlspecialchars($related['name']); ?></h3>
                            <div class="card-price" style="font-size: 1.25rem;"><?php echo format_price($related['price']); ?></div>
                            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm btn-block">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
