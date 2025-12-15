<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$page_title = 'My Profile';

// Require login
require_login();

$errors = [];
$success = '';

// Get current user
$user = get_current_user($pdo);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        if (isset($_POST['update_profile'])) {
            $name = sanitize($_POST['name'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $address = sanitize($_POST['address'] ?? '');
            
            $result = update_user_profile($pdo, $_SESSION['user_id'], $name, $phone, $address);
            
            if ($result['success']) {
                $success = $result['message'];
                $user = get_current_user($pdo); // Refresh user data
            } else {
                $errors[] = $result['message'];
            }
        } elseif (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($new_password !== $confirm_password) {
                $errors[] = 'New passwords do not match';
            } else {
                $result = change_password($pdo, $_SESSION['user_id'], $current_password, $new_password);
                
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $errors[] = $result['message'];
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <h1>My Profile</h1>
    
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
    
    <div class="grid grid-2" style="max-width: 1000px;">
        <!-- Profile Information -->
        <div class="card">
            <div class="card-body">
                <h2>Profile Information</h2>
                <form method="POST" action="">
                    <?php echo csrf_token_field(); ?>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               disabled style="background-color: #f0f0f0;">
                        <small style="color: #666;">Email cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-body">
                <h2>Change Password</h2>
                <form method="POST" action="">
                    <?php echo csrf_token_field(); ?>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required 
                               minlength="6" placeholder="At least 6 characters">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               minlength="6">
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card mt-4" style="max-width: 1000px;">
        <div class="card-body">
            <h2>Account Information</h2>
            <p><strong>Account Type:</strong> <?php echo ucfirst($user['role']); ?></p>
            <p><strong>Member Since:</strong> <?php echo format_date($user['created_at']); ?></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
