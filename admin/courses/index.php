<?php
/**
 * Manage Courses
 * Create, edit, and manage courses
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

$page_title = 'Manage Courses';

// Get all courses with category names
try {
    $stmt = $pdo->query("
        SELECT c.id, c.title, c.difficulty_level, c.is_published, c.created_at,
               cat.name as category,
               COUNT(e.id) as enrollments
        FROM courses c
        JOIN categories cat ON c.category_id = cat.id
        LEFT JOIN enrollments e ON c.id = e.course_id
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for filter
    $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY display_order');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
    $courses = [];
    $categories = [];
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="section-title mb-0">Manage Courses</h1>
                <a href="<?php echo BASE_URL; ?>/admin/courses/create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Course
                </a>
            </div>
            
            <!-- Courses Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (count($courses) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Level</th>
                                        <th>Enrollments</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark"><?php echo htmlspecialchars($course['category']); ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $level_class = match($course['difficulty_level']) {
                                                    'beginner' => 'success',
                                                    'intermediate' => 'warning',
                                                    'advanced' => 'danger',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $level_class; ?>"><?php echo ucfirst($course['difficulty_level']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $course['enrollments']; ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $pub_class = $course['is_published'] ? 'success' : 'secondary';
                                                $pub_text = $course['is_published'] ? 'Published' : 'Draft';
                                                ?>
                                                <span class="badge bg-<?php echo $pub_class; ?>"><?php echo $pub_text; ?></span>
                                            </td>
                                            <td><?php echo format_date($course['created_at'], 'M d, Y'); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/admin/courses/edit.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle"></i> No courses found. <a href="<?php echo BASE_URL; ?>/admin/courses/create.php">Create the first course</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
