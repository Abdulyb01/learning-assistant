<?php
/**
 * Logout Page
 * Handles user logout and session destruction
 * @author Learning Assistant Team
 * @version 1.0.0
 */

session_start();

define('BASE_URL', 'http://localhost/learning-assistant');
require_once '../config/database.php';
require_once '../functions/auth.php';

// Logout user
logout_user();

// Set success message
$_SESSION['alert'] = ['type' => 'success', 'message' => 'You have been logged out successfully.'];

// Redirect to login page
header('Location: ' . BASE_URL . '/auth/login.php');
exit();

?>
