<?php
/**
 * Admin Dashboard
 * Main admin control panel with statistics and overview
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

$page_title = 'Admin Dashboard';

// Get statistics
try {
    // Total users
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users WHERE role = "student"');
    $total_students = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total courses
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM courses WHERE is_published = 1');
    $total_courses = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total enrollments
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM enrollments WHERE enrollment_status = "active"');
    $total_enrollments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total lessons
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM lessons WHERE is_published = 1');
    $total_lessons = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Completed courses
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM enrollments WHERE enrollment_status = "completed"');
    $completed_courses = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Average course rating
    $stmt = $pdo->query('SELECT AVG(rating) as avg_rating FROM courses');
    $avg_rating = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];
    $avg_rating = $avg_rating ? round($avg_rating, 2) : 0;
    
    // Recent students
    $stmt = $pdo->query('SELECT u.id, u.username, u.email, u.created_at FROM users u WHERE u.role = "student" ORDER BY u.created_at DESC LIMIT 5');
    $recent_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top courses by enrollment
    $stmt = $pdo->query('SELECT c.id, c.title, COUNT(e.id) as enrollment_count FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id GROUP BY c.id ORDER BY enrollment_count DESC LIMIT 5');
    $top_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // New feedback
    $stmt = $pdo->query('SELECT f.id, u.username, f.rating, f.comment, f.created_at FROM feedback f JOIN users u ON f.student_id = u.id WHERE f.status = "new" ORDER BY f.created_at DESC LIMIT 5');
    $new_feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log($e->getMessage());
    $total_students = 0;
    $total_courses = 0;
    $total_enrollments = 0;
    $total_lessons = 0;
    $completed_courses = 0;
    $avg_rating = 0;
    $recent_students = [];
    $top_courses = [];
    $new_feedback = [];
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
            <h1 class="section-title mb-4">Admin Dashboard</h1>
            
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="stat-label">Total Students</h6>
                                    <h2 class="stat-number"><?php echo $total_students; ?></h2>
                                </div>
                                <i class="bi bi-people" style="font-size: 2rem; color: #0056b3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="stat-label">Published Courses</h6>
                                    <h2 class="stat-number"><?php echo $total_courses; ?></h2>
                                </div>
                                <i class="bi bi-book" style="font-size: 2rem; color: #28a745;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="stat-label">Active Enrollments</h6>
                                    <h2 class="stat-number"><?php echo $total_enrollments; ?></h2>
                                </div>
                                <i class="bi bi-graph-up" style="font-size: 2rem; color: #ffc107;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="stat-label">Avg Rating</h6>
                                    <h2 class="stat-number"><?php echo $avg_rating; ?></h2>
                                </div>
                                <i class="bi bi-star" style="font-size: 2rem; color: #dc3545;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <!-- Top Courses -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-fire"></i> Top Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($top_courses) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($top_courses as $course): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($course['title']); ?></h6>
                                                <small class="text-muted">by enrollment</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill"><?php echo $course['enrollment_count']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No courses yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Students -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-people"></i> Recent Students</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($recent_students) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_students as $student): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($student['username']); ?></h6>
                                                <small><?php echo format_date($student['created_at'], 'M d'); ?></small>
                                            </div>
                                            <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No students yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <!-- New Feedback -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-chat-dots"></i> New Feedback</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($new_feedback) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Rating</th>
                                                <th>Comment</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($new_feedback as $feedback): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                                                    <td>
                                                        <?php for ($i = 0; $i < $feedback['rating']; $i++): ?>
                                                            <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                                                        <?php endfor; ?>
                                                    </td>
                                                    <td><?php echo substr(htmlspecialchars($feedback['comment']), 0, 30) . '...'; ?></td>
                                                    <td><?php echo format_date($feedback['created_at'], 'M d'); ?></td>
                                                    <td>
                                                        <a href="<?php echo BASE_URL; ?>/admin/feedback/" class="btn btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No new feedback</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
