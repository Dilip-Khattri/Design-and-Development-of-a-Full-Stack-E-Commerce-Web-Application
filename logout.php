<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Logout user
logout_user();

// Set success message
$_SESSION['success'] = 'You have been logged out successfully';

// Redirect to login page
redirect('login.php');
?>
