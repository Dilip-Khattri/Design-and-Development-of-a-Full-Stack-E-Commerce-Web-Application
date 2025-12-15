<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Products';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

// Get products
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause 
        ORDER BY p.created_at DESC 
        LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories for filter
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cat_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <h1>Products</h1>
    
    <!-- Search and Filters -->
    <div class="products-header">
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button onclick="searchProducts()" class="btn btn-primary">Search</button>
        </div>
    </div>
    
    <div class="category-filter">
        <a href="products.php" class="btn <?php echo !$category ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <?php foreach ($categories as $cat): ?>
            <a href="products.php?category=<?php echo $cat['slug']; ?>" 
               class="btn <?php echo $category === $cat['slug'] ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="card">
            <div class="card-body text-center">
                <h2>No products found</h2>
                <p>Try adjusting your search or filters</p>
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        </div>
    <?php else: ?>
        <p style="margin: 1rem 0;">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
        
        <div class="grid grid-3">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <img src="<?php echo $product['image'] ? 'uploads/products/' . htmlspecialchars($product['image']) : 'assets/images/placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="card-img">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="card-text"><?php echo truncate($product['description'], 80); ?></p>
                        <div class="card-price"><?php echo format_price($product['price']); ?></div>
                        <p style="color: #666; font-size: 0.9rem;">
                            Category: <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                        </p>
                        <p style="color: <?php echo $product['stock'] > 0 ? '#27ae60' : '#e74c3c'; ?>; font-weight: bold;">
                            <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ')' : 'Out of Stock'; ?>
                        </p>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-primary" style="flex: 1;">View Details</a>
                            <?php if (is_logged_in() && $product['stock'] > 0): ?>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                        class="btn btn-success">Add to Cart</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
