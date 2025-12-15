<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$page_title = 'Login';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$success = '';

// Check for flash messages
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $result = login_user($pdo, $email, $password);
        
        if ($result['success']) {
            // Redirect to appropriate page
            if (is_admin()) {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 3rem auto;">
    <div class="form-container">
        <h1 class="text-center mb-3">Login</h1>
        
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
        
        <form method="POST" action="">
            <?php echo csrf_token_field(); ?>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="text-center mt-3">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        
        <div class="alert alert-info mt-3">
            <strong>Demo Credentials:</strong><br>
            Admin: admin@example.com / admin123<br>
            User: user@example.com / user123
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
