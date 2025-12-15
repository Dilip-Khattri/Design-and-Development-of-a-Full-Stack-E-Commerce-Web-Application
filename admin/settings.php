<?php
$page_title = 'Site Settings';
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';

$errors = [];
$success = '';

// Get current settings
$settings = get_all_settings($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    // Site Name
    if (isset($_POST['site_name'])) {
        $site_name = sanitize($_POST['site_name']);
        if (empty($site_name)) {
            $errors[] = 'Site name is required';
        } else {
            update_setting($pdo, 'site_name', $site_name);
        }
    }
    
    // Tax Rate
    if (isset($_POST['tax_rate'])) {
        $tax_rate = floatval($_POST['tax_rate']);
        if ($tax_rate < 0 || $tax_rate > 100) {
            $errors[] = 'Tax rate must be between 0 and 100';
        } else {
            update_setting($pdo, 'tax_rate', $tax_rate);
        }
    }
    
    // Delivery Fee
    if (isset($_POST['delivery_fee'])) {
        $delivery_fee = floatval($_POST['delivery_fee']);
        if ($delivery_fee < 0) {
            $errors[] = 'Delivery fee cannot be negative';
        } else {
            update_setting($pdo, 'delivery_fee', $delivery_fee);
        }
    }
    
    // Free Shipping Threshold
    if (isset($_POST['free_shipping_threshold'])) {
        $threshold = floatval($_POST['free_shipping_threshold']);
        if ($threshold < 0) {
            $errors[] = 'Free shipping threshold cannot be negative';
        } else {
            update_setting($pdo, 'free_shipping_threshold', $threshold);
        }
    }
    
    // Payment Gateway
    if (isset($_POST['payment_gateway'])) {
        $gateway = sanitize($_POST['payment_gateway']);
        update_setting($pdo, 'payment_gateway', $gateway);
    }
    
    // Payment Gateway Key
    if (isset($_POST['payment_gateway_key'])) {
        update_setting($pdo, 'payment_gateway_key', $_POST['payment_gateway_key']);
    }
    
    // Payment Gateway Secret
    if (isset($_POST['payment_gateway_secret'])) {
        update_setting($pdo, 'payment_gateway_secret', $_POST['payment_gateway_secret']);
    }
    
    // Handle logo upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if ($_FILES['site_logo']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Logo upload failed';
        } elseif ($_FILES['site_logo']['size'] > $max_size) {
            $errors[] = 'Logo file size exceeds 2MB limit';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['site_logo']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                $errors[] = 'Invalid logo file type. Only JPG, PNG, GIF, and SVG allowed';
            } else {
                $extension = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $extension;
                $upload_path = __DIR__ . '/../uploads/';
                
                // Create uploads directory if it doesn't exist
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }
                
                $destination = $upload_path . $filename;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $destination)) {
                    // Delete old logo if exists
                    $old_logo = get_setting($pdo, 'site_logo', '');
                    if ($old_logo && file_exists($upload_path . $old_logo)) {
                        unlink($upload_path . $old_logo);
                    }
                    
                    update_setting($pdo, 'site_logo', $filename);
                } else {
                    $errors[] = 'Failed to save logo file';
                }
            }
        }
    }
    
    // Remove logo if requested
    if (isset($_POST['remove_logo'])) {
        $old_logo = get_setting($pdo, 'site_logo', '');
        if ($old_logo && file_exists(__DIR__ . '/../uploads/' . $old_logo)) {
            unlink(__DIR__ . '/../uploads/' . $old_logo);
        }
        update_setting($pdo, 'site_logo', '');
    }
    
    if (empty($errors)) {
        $success = 'Settings updated successfully';
        // Refresh settings
        $settings = get_all_settings($pdo);
    }
}
?>

<div class="admin-header">
    <h1>Site Settings</h1>
    <div class="breadcrumb">Admin / Settings</div>
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
    <form method="POST" enctype="multipart/form-data">
        <?php echo csrf_token_field(); ?>
        
        <h2>General Settings</h2>
        
        <div class="form-group">
            <label for="site_name">Website Name *</label>
            <input type="text" id="site_name" name="site_name" required 
                   value="<?php echo htmlspecialchars($settings['site_name'] ?? 'E-Commerce Store'); ?>">
        </div>
        
        <div class="form-group">
            <label for="site_logo">Website Logo</label>
            <?php if (!empty($settings['site_logo'])): ?>
                <div class="image-preview" style="margin-bottom: 1rem;">
                    <img src="../uploads/<?php echo htmlspecialchars($settings['site_logo']); ?>" 
                         alt="Current Logo" style="max-height: 100px;">
                    <div style="margin-top: 0.5rem;">
                        <label>
                            <input type="checkbox" name="remove_logo" value="1">
                            Remove current logo
                        </label>
                    </div>
                </div>
            <?php endif; ?>
            <input type="file" id="site_logo" name="site_logo" accept="image/*">
            <small>Max size: 2MB. Supported: JPG, PNG, GIF, SVG. Recommended size: 200x50px</small>
        </div>
        
        <hr style="margin: 2rem 0;">
        
        <h2>Pricing & Shipping</h2>
        
        <div class="form-row">
            <div class="form-group">
                <label for="tax_rate">Tax Rate (%)</label>
                <input type="number" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100" 
                       value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '10'); ?>">
                <small>Enter tax percentage (e.g., 10 for 10%)</small>
            </div>
            
            <div class="form-group">
                <label for="delivery_fee">Delivery Fee ($)</label>
                <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0" 
                       value="<?php echo htmlspecialchars($settings['delivery_fee'] ?? '10'); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="free_shipping_threshold">Free Shipping Threshold ($)</label>
            <input type="number" id="free_shipping_threshold" name="free_shipping_threshold" step="0.01" min="0" 
                   value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '50'); ?>">
            <small>Orders above this amount get free shipping</small>
        </div>
        
        <hr style="margin: 2rem 0;">
        
        <h2>Payment Gateway Settings</h2>
        
        <div class="form-group">
            <label for="payment_gateway">Payment Gateway</label>
            <select id="payment_gateway" name="payment_gateway">
                <option value="dummy" <?php echo ($settings['payment_gateway'] ?? 'dummy') == 'dummy' ? 'selected' : ''; ?>>Dummy (Testing)</option>
                <option value="stripe" <?php echo ($settings['payment_gateway'] ?? '') == 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                <option value="paypal" <?php echo ($settings['payment_gateway'] ?? '') == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                <option value="razorpay" <?php echo ($settings['payment_gateway'] ?? '') == 'razorpay' ? 'selected' : ''; ?>>Razorpay</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="payment_gateway_key">Payment Gateway API Key</label>
            <input type="text" id="payment_gateway_key" name="payment_gateway_key" 
                   value="<?php echo htmlspecialchars($settings['payment_gateway_key'] ?? ''); ?>" 
                   placeholder="Enter your payment gateway API key">
            <small>Leave empty if using dummy payment gateway</small>
        </div>
        
        <div class="form-group">
            <label for="payment_gateway_secret">Payment Gateway Secret Key</label>
            <input type="password" id="payment_gateway_secret" name="payment_gateway_secret" 
                   value="<?php echo htmlspecialchars($settings['payment_gateway_secret'] ?? ''); ?>" 
                   placeholder="Enter your payment gateway secret key">
            <small>Keep this confidential. Leave empty if using dummy payment gateway</small>
        </div>
        
        <div class="alert alert-info" style="margin-top: 1rem;">
            <strong>Note:</strong> Payment gateway integration requires additional configuration. 
            Currently, the system uses a dummy payment gateway for demonstration purposes.
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-success">Save Settings</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
