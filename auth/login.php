<?php
/**
 * Login Page
 * User authentication and login form
 * @author Learning Assistant Team
 * @version 1.0.0
 */

session_start();

define('BASE_URL', 'http://localhost/learning-assistant');
require_once '../config/database.php';
require_once '../functions/helpers.php';
require_once '../functions/auth.php';

// Check if already logged in
if (is_authenticated()) {
    if (check_role('admin')) {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/student/dashboard.php');
    }
    exit();
}

// Check remember me
if (!is_authenticated() && isset($_COOKIE['remember_token'])) {
    if (check_remember_token()) {
        if (check_role('admin')) {
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
        } else {
            header('Location: ' . BASE_URL . '/student/dashboard.php');
        }
        exit();
    }
}

$page_title = 'Login';
$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember_me']) ? true : false;
        
        $result = login_user($email, $password, $remember);
        
        if ($result['success']) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => $result['message']];
            
            // Redirect to dashboard
            if (check_role('admin')) {
                header('Location: ' . BASE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/student/dashboard.php');
            }
            exit();
        } else {
            $error_message = $result['message'];
        }
    }
}

// Generate CSRF token
$csrf_token = generate_csrf_token();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </h2>
                    <p class="small mb-0 mt-2">Welcome back to Learning Assistant</p>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>/auth/login.php" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="your@email.com" 
                                required
                            >
                            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password" 
                                required
                            >
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="remember_me" 
                                name="remember_me"
                            >
                            <label class="form-check-label" for="remember_me">
                                Remember me for 30 days
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="text-center mb-3">
                        <hr>
                        <small class="text-muted">Don't have an account?</small>
                    </div>
                    
                    <!-- Register Link -->
                    <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-outline-primary w-100">
                        <i class="bi bi-person-plus"></i> Create Account
                    </a>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="card mt-4 bg-light border-info">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-info-circle"></i> Demo Credentials
                    </h6>
                    <p class="small mb-2">
                        <strong>Student:</strong><br>
                        Email: student1@example.com<br>
                        Password: Student@123
                    </p>
                    <p class="small">
                        <strong>Admin:</strong><br>
                        Email: admin@learningassistant.com<br>
                        Password: Admin@12345
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
