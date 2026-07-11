<?php
/**
 * Manage Students
 * View and manage all student accounts
 * @author Learning Assistant Team
 * @version 1.0.0
 */

session_start();

define('BASE_URL', 'http://localhost/learning-assistant');
require_once '../config/database.php';
require_once '../functions/helpers.php';
require_once '../functions/auth.php';

// Check if user is admin
require_admin();

$page_title = 'Manage Students';

// Get all students
try {
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.status, u.created_at,
               COUNT(e.id) as enrollments,
               SUM(CASE WHEN e.enrollment_status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM users u
        LEFT JOIN enrollments e ON u.id = e.student_id
        WHERE u.role = 'student'
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    $students = [];
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="section-title mb-0">Manage Students</h1>
                <div>
                    <span class="badge bg-primary"><?php echo count($students); ?> Students</span>
                </div>
            </div>
            
            <!-- Students Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (count($students) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Enrollments</th>
                                        <th>Completed</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $student['enrollments']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $student['completed'] ?? 0; ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $status_class = $student['status'] === 'active' ? 'success' : 'danger';
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($student['status']); ?></span>
                                            </td>
                                            <td><?php echo format_date($student['created_at'], 'M d, Y'); ?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger" title="Suspend">
                                                    <i class="bi bi-exclamation-circle"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle"></i> No students found
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
