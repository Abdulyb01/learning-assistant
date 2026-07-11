<?php
/**
 * Authentication Check
 * Verify user is logged in and has required role
 * Include at start of protected pages
 * @author Learning Assistant Team
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/learning-assistant');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/auth.php';
require_once __DIR__ . '/functions/helpers.php';

// Check if user is logged in, if not redirect to login
if (!is_authenticated()) {
    // Check remember me token
    if (!check_remember_token()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

// Regenerate session ID periodically for security
if (!isset($_SESSION['last_regeneration'])) {
    regenerate_session();
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    regenerate_session();
    $_SESSION['last_regeneration'] = time();
}

?>
