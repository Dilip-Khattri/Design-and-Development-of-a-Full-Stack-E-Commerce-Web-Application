<?php
$page_title = 'Edit Product';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

$errors = [];
$success = '';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    set_flash('error', 'Invalid product ID');
    redirect('products.php');
}

// Get product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found');
    redirect('products.php');
}

// Get categories
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cat_stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validate
    if (empty($name)) {
        $errors[] = 'Product name is required';
    }
    
    if (empty($slug)) {
        $slug = generate_slug($name);
    }
    
    // Check if slug exists (excluding current product)
    $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $product_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Product slug already exists';
    }
    
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }
    
    if ($stock < 0) {
        $errors[] = 'Stock cannot be negative';
    }
    
    // Handle image upload
    $image_filename = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = upload_product_image($_FILES['image']);
        if ($upload_result['success']) {
            // Delete old image
            if ($product['image']) {
                delete_product_image($product['image']);
            }
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // Update if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock = ?, 
                                  image = ?, category_id = ?, featured = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $price, $stock, $image_filename, $category_id ?: null, $featured, $product_id]);
            
            set_flash('success', 'Product updated successfully');
            redirect('products.php');
        } catch (PDOException $e) {
            $errors[] = 'Failed to update product';
        }
    } else {
        // Reload product data for form
        $product['name'] = $name;
        $product['slug'] = $slug;
        $product['description'] = $description;
        $product['price'] = $price;
        $product['stock'] = $stock;
        $product['category_id'] = $category_id;
        $product['featured'] = $featured;
    }
}
?>

<div class="admin-header">
    <h1>Edit Product</h1>
    <div class="breadcrumb">Admin / Products / Edit</div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data" onsubmit="return validateProductForm()">
        <?php echo csrf_token_field(); ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="product-name">Product Name *</label>
                <input type="text" id="product-name" name="name" required 
                       value="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="product-slug">Slug *</label>
                <input type="text" id="product-slug" name="slug" 
                       value="<?php echo htmlspecialchars($product['slug']); ?>">
                <small>URL-friendly version of the name</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="product-price">Price ($) *</label>
                <input type="number" id="product-price" name="price" step="0.01" min="0" required 
                       value="<?php echo htmlspecialchars($product['price']); ?>">
            </div>
            
            <div class="form-group">
                <label for="product-stock">Stock Quantity *</label>
                <input type="number" id="product-stock" name="stock" min="0" required 
                       value="<?php echo htmlspecialchars($product['stock']); ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id">
                    <option value="">None</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="featured" value="1" 
                           <?php echo $product['featured'] ? 'checked' : ''; ?>>
                    Featured Product
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="product-image">Product Image</label>
            <?php if ($product['image']): ?>
                <div class="image-preview">
                    <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="Current image" id="image-preview">
                </div>
            <?php endif; ?>
            <input type="file" id="product-image" name="image" accept="image/*">
            <small>Max size: 5MB. Supported: JPG, PNG, GIF. Leave empty to keep current image.</small>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Update Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
