<?php
/**
 * Student Feedback Management
 * View and respond to student feedback
 * @author Learning Assistant Team
 * @version 1.0.0
 */

session_start();

define('BASE_URL', 'http://localhost/learning-assistant');
require_once '../../config/database.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth.php';

// Check if user is admin
require_admin();

$page_title = 'Feedback';

// Get all feedback
try {
    $stmt = $pdo->query("
        SELECT f.id, f.rating, f.comment, f.status, f.created_at,
               u.username, u.email,
               c.title as course_title
        FROM feedback f
        JOIN users u ON f.student_id = u.id
        LEFT JOIN courses c ON f.course_id = c.id
        ORDER BY f.created_at DESC
    ");
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    $feedback = [];
}

include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../../includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 col-lg-10">
            <h1 class="section-title mb-4">Student Feedback</h1>
            
            <!-- Feedback Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (count($feedback) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Rating</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['username']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['course_title'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php for ($i = 0; $i < $item['rating']; $i++): ?>
                                                    <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td><?php echo substr(htmlspecialchars($item['comment']), 0, 50) . '...'; ?></td>
                                            <td>
                                                <?php 
                                                $status_class = match($item['status']) {
                                                    'new' => 'warning',
                                                    'reviewed' => 'info',
                                                    'resolved' => 'success',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($item['status']); ?></span>
                                            </td>
                                            <td><?php echo format_date($item['created_at'], 'M d, Y'); ?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary" title="Reply" data-bs-toggle="modal" data-bs-target="#replyModal">
                                                    <i class="bi bi-reply"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle"></i> No feedback found
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
