<?php
/**
 * Helper Functions
 * Contains reusable utility functions for the application
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a specific page
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "Please login to continue";
        redirect('login.php');
    }
}

/**
 * Require admin access
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        $_SESSION['error'] = "Access denied. Admin only.";
        redirect('/index.php');
    }
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF token input field
 */
function csrf_token_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION[CSRF_TOKEN_NAME] . '">';
}

/**
 * Get CSRF token value
 */
function csrf_token() {
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Display flash message
 */
function display_flash() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        echo '<div class="alert alert-' . $type . '" id="flash-message">' . htmlspecialchars($message) . '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Format price
 */
function format_price($price) {
    return '$' . number_format($price, 2);
}

/**
 * Generate slug from string
 */
function generate_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Upload product image
 */
function upload_product_image($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed with error code: ' . $file['error']];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF allowed'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = UPLOAD_PATH . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
    
    return ['success' => true, 'filename' => $filename];
}

/**
 * Delete product image
 */
function delete_product_image($filename) {
    if ($filename && file_exists(UPLOAD_PATH . $filename)) {
        unlink(UPLOAD_PATH . $filename);
    }
}

/**
 * Get cart item count
 */
function get_cart_count($pdo, $user_id) {
    if (!$user_id) return 0;
    
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

/**
 * Format date
 */
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Get order status badge class
 */
function get_status_badge($status) {
    $badges = [
        'pending' => 'warning',
        'paid' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    return $badges[$status] ?? 'secondary';
}
?>
