<?php
/**
 * Authentication Functions
 * Handles user login, registration, and session management
 * @author Learning Assistant Team
 * @version 1.0.0
 */

define('BASE_URL', 'http://localhost/learning-assistant');
require_once __DIR__ . '/../config/database.php';

/**
 * Hash password using bcrypt
 * @param string $password Plain password
 * @return string Hashed password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password against hash
 * @param string $password Plain password
 * @param string $hash Password hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Register new user
 * @param array $data User data (username, email, password, first_name, last_name)
 * @return array Result array with status and message
 */
function register_user($data) {
    global $pdo;
    
    // Validate input
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';
    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    
    // Validate fields
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    // Validate username
    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username must be at least 3 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        return ['success' => false, 'message' => 'Username can only contain letters, numbers, underscore and hyphen'];
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Validate password
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters'];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return ['success' => false, 'message' => 'Password must contain uppercase letter'];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return ['success' => false, 'message' => 'Password must contain lowercase letter'];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return ['success' => false, 'message' => 'Password must contain number'];
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }
    
    // Check if username exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $password_hash = hash_password($password);
    
    // Insert user
    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, first_name, last_name, role, status, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, 'student', 'active', false]);
        $user_id = $pdo->lastInsertId();
        
        // Create user profile
        $stmt = $pdo->prepare('INSERT INTO profiles (user_id) VALUES (?)');
        $stmt->execute([$user_id]);
        
        // Create user settings
        $stmt = $pdo->prepare('INSERT INTO settings (user_id, theme, language) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, 'light', 'en']);
        
        return ['success' => true, 'message' => 'Registration successful. Please login.', 'user_id' => $user_id];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login user
 * @param string $email Email address
 * @param string $password Password
 * @param bool $remember Remember me flag
 * @return array Result array with status and message
 */
function login_user($email, $password, $remember = false) {
    global $pdo;
    
    $email = trim($email);
    
    // Validate input
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required'];
    }
    
    // Find user
    $stmt = $pdo->prepare('SELECT id, username, email, password_hash, role, status FROM users WHERE email = ?');
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if account is active
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account is not active'];
    }
    
    // Verify password
    if (!verify_password($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    // Generate remember token if checked
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
        $stmt->execute([$token, $user['id']]);
        
        // Set cookie for 30 days
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
    }
    
    // Update last login
    $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
    $stmt->execute([$user['id']]);
    
    return ['success' => true, 'message' => 'Login successful', 'user_id' => $user['id'], 'role' => $user['role']];
}

/**
 * Logout user
 * @return void
 */
function logout_user() {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        // Clear remember token
        $stmt = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    // Clear cookies
    setcookie('remember_token', '', time() - 3600, '/');
    
    // Destroy session
    session_destroy();
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_authenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Check if user has specific role
 * @param string $role Role to check
 * @return bool
 */
function check_role($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Require login
 * @return void
 */
function require_login() {
    if (!is_authenticated()) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

/**
 * Require admin role
 * @return void
 */
function require_admin() {
    require_login();
    if (!check_role('admin')) {
        header('Location: ' . BASE_URL . '/');
        exit();
    }
}

/**
 * Require student role
 * @return void
 */
function require_student() {
    require_login();
    if (!check_role('student')) {
        header('Location: ' . BASE_URL . '/');
        exit();
    }
}

/**
 * Regenerate session ID (security)
 * @return void
 */
function regenerate_session() {
    session_regenerate_id(true);
}

/**
 * Get current user ID
 * @return int|null
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 * @return array|null
 */
function get_current_user() {
    global $pdo;
    
    $user_id = get_current_user_id();
    if (!$user_id) return null;
    
    $stmt = $pdo->prepare('SELECT u.*, p.profile_picture, p.bio, p.preferred_learning_style FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Check remember me token
 * @return bool
 */
function check_remember_token() {
    global $pdo;
    
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $token = $_COOKIE['remember_token'];
    
    $stmt = $pdo->prepare('SELECT id, username, email, role FROM users WHERE remember_token = ? AND status = "active"');
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() === 0) {
        return false;
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    return true;
}

?>
