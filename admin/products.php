<?php
$page_title = 'Products Management';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

// Handle delete
if (isset($_POST['delete_id']) && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $delete_id = (int)$_POST['delete_id'];
    
    // Get product image to delete
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$delete_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Delete image file
        if ($product['image']) {
            delete_product_image($product['image']);
        }
        
        // Delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$delete_id]);
        
        set_flash('success', 'Product deleted successfully');
        redirect('products.php');
    }
}

// Get all products
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause 
        ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cat_stmt->fetchAll();
?>

<div class="admin-header">
    <h1>Products Management</h1>
    <div class="breadcrumb">Admin / Products</div>
</div>

<?php display_flash(); ?>

<div class="admin-table-container">
    <div class="table-header">
        <h2>All Products (<?php echo count($products); ?>)</h2>
        <div class="table-actions">
            <a href="add-product.php" class="btn btn-success">Add New Product</a>
        </div>
    </div>
    
    <!-- Filters -->
    <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 0.5rem; flex: 1;">
            <input type="text" name="search" placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search); ?>" 
                   style="flex: 1; max-width: 400px;">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search || $category): ?>
                <a href="products.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
        
        <select onchange="window.location='products.php?category=' + this.value">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="<?php echo $product['image'] ? '../uploads/products/' . htmlspecialchars($product['image']) : '../assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                    <br>
                                    <small style="color: #666;"><?php echo truncate($product['description'], 60); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                        <td><strong><?php echo format_price($product['price']); ?></strong></td>
                        <td>
                            <span style="color: <?php echo $product['stock'] > 10 ? '#27ae60' : ($product['stock'] > 0 ? '#f39c12' : '#e74c3c'); ?>;">
                                <?php echo $product['stock']; ?>
                            </span>
                        </td>
                        <td><?php echo $product['featured'] ? '⭐ Yes' : 'No'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirmDelete('product', <?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>');">
                                    <?php echo csrf_token_field(); ?>
                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
