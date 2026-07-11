<?php
/**
 * Password Reset Page (Future Implementation)
 * Handles password reset via email token
 * @author Learning Assistant Team
 * @version 1.0.0
 */

session_start();

define('BASE_URL', 'http://localhost/learning-assistant');
require_once '../config/database.php';
require_once '../functions/helpers.php';
require_once '../functions/auth.php';

$page_title = 'Reset Password';
$step = isset($_GET['step']) ? $_GET['step'] : 'request';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$error_message = '';
$success_message = '';

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="bi bi-key"></i> Reset Password
                    </h2>
                </div>
                
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Enter your email to receive a password reset link.</p>
                    
                    <form method="POST" action="#">
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
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-send"></i> Send Reset Link
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <hr>
                        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
