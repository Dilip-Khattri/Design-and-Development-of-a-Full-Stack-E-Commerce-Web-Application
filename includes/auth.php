<?php
/**
 * Authentication Functions
 * Handles user registration, login, and authentication
 */

/**
 * Register a new user
 */
function register_user($pdo, $name, $email, $password) {
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$name, $email, $hashed_password]);
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

/**
 * Login user
 */
function login_user($pdo, $email, $password) {
    // Validate inputs
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required'];
    }
    
    // Get user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

/**
 * Logout user
 */
function logout_user() {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

/**
 * Get current user
 */
function get_current_user($pdo) {
    if (!is_logged_in()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Update user profile
 */
function update_user_profile($pdo, $user_id, $name, $phone, $address) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $address, $user_id]);
        
        // Update session name
        $_SESSION['user_name'] = $name;
        
        return ['success' => true, 'message' => 'Profile updated successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
}

/**
 * Change user password
 */
function change_password($pdo, $user_id, $current_password, $new_password) {
    // Get current user
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    // Validate new password
    if (strlen($new_password) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters'];
    }
    
    // Hash and update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        return ['success' => true, 'message' => 'Password changed successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to change password'];
    }
}
?>
