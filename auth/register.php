<?php
/**
 * Registration Page
 * User registration and account creation form
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

$page_title = 'Register';
$error_message = '';
$success_message = '';
$form_data = [];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $form_data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? ''
        ];
        
        $result = register_user($form_data);
        
        if ($result['success']) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => $result['message']];
            header('Location: ' . BASE_URL . '/auth/login.php');
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
                        <i class="bi bi-person-plus"></i> Create Account
                    </h2>
                    <p class="small mb-0 mt-2">Join Learning Assistant today</p>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo BASE_URL; ?>/auth/register.php" id="registerForm" onsubmit="return validateRegistrationForm(this)">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">
                                <i class="bi bi-person"></i> First Name
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="first_name" 
                                name="first_name" 
                                placeholder="John" 
                                value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>"
                                required
                            >
                        </div>
                        
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="last_name" class="form-label">
                                <i class="bi bi-person"></i> Last Name
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="last_name" 
                                name="last_name" 
                                placeholder="Doe" 
                                value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>"
                                required
                            >
                        </div>
                        
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-at"></i> Username
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="username" 
                                name="username" 
                                placeholder="johndoe" 
                                value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>"
                                required
                            >
                            <small class="form-text text-muted">3-50 characters, letters, numbers, underscore and hyphen only.</small>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="john@example.com" 
                                value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                required
                            >
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Enter password" 
                                required
                                oninput="showPasswordStrength(this, document.getElementById('passwordStrength'))"
                            >
                            <small class="form-text text-muted" id="passwordStrength" style="display: block; margin-top: 5px;"></small>
                            <small class="form-text text-muted">Minimum 8 characters, must include uppercase, lowercase, and number.</small>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="bi bi-lock-check"></i> Confirm Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="Re-enter password" 
                                required
                            >
                        </div>
                        
                        <!-- Terms Agreement -->
                        <div class="mb-3 form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="agree_terms" 
                                required
                            >
                            <label class="form-check-label" for="agree_terms">
                                I agree to the <a href="#" target="_blank">Terms of Service</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-person-plus"></i> Create Account
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="text-center mb-3">
                        <hr>
                        <small class="text-muted">Already have an account?</small>
                    </div>
                    
                    <!-- Login Link -->
                    <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-outline-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Back to Login
                    </a>
                </div>
            </div>
            
            <!-- Password Requirements -->
            <div class="card mt-4 bg-light border-info">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-info-circle"></i> Password Requirements
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li>Minimum 8 characters</li>
                        <li>At least one uppercase letter (A-Z)</li>
                        <li>At least one lowercase letter (a-z)</li>
                        <li>At least one number (0-9)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
