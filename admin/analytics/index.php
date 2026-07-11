<?php
/**
 * View Analytics
 * Performance analytics and reports
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

$page_title = 'Analytics';

// Get analytics data
try {
    // Course completion rate
    $stmt = $pdo->query('SELECT AVG(progress_percentage) as avg_progress FROM enrollments');
    $avg_progress = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_progress'], 1);
    
    // Quiz pass rate
    $stmt = $pdo->query('SELECT 
        SUM(CASE WHEN status = "passed" THEN 1 ELSE 0 END) * 100 / COUNT(*) as pass_rate
        FROM results
    ');
    $pass_rate = round($stmt->fetch(PDO::FETCH_ASSOC)['pass_rate'] ?? 0, 1);
    
    // Student engagement
    $stmt = $pdo->query('SELECT COUNT(DISTINCT student_id) as active_students FROM progress WHERE last_accessed >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
    $active_students = $stmt->fetch(PDO::FETCH_ASSOC)['active_students'];
    
    // Enrollments by course
    $stmt = $pdo->query('SELECT c.title, COUNT(e.id) as count FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id GROUP BY c.id ORDER BY count DESC LIMIT 10');
    $enrollments_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log($e->getMessage());
    $avg_progress = 0;
    $pass_rate = 0;
    $active_students = 0;
    $enrollments_data = [];
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
            <h1 class="section-title mb-4">Analytics & Reports</h1>
            
            <!-- Key Metrics -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h6 class="stat-label">Avg Course Progress</h6>
                            <h2 class="stat-number"><?php echo $avg_progress; ?>%</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h6 class="stat-label">Quiz Pass Rate</h6>
                            <h2 class="stat-number"><?php echo $pass_rate; ?>%</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h6 class="stat-label">Active Students (7d)</h6>
                            <h2 class="stat-number"><?php echo $active_students; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enrollments Chart Data -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Top 10 Courses by Enrollment</h5>
                </div>
                <div class="card-body">
                    <?php if (count($enrollments_data) > 0): ?>
                        <canvas id="enrollmentsChart" height="80"></canvas>
                    <?php else: ?>
                        <p class="text-muted mb-0">No data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php if (count($enrollments_data) > 0): ?>
<script>
    const enrollmentLabels = <?php echo json_encode(array_map(fn($d) => $d['title'], $enrollments_data)); ?>;
    const enrollmentData = <?php echo json_encode(array_map(fn($d) => $d['count'], $enrollments_data)); ?>;
    
    const ctx = document.getElementById('enrollmentsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: enrollmentLabels,
            datasets: [{
                label: 'Enrollments',
                data: enrollmentData,
                backgroundColor: '#0056b3',
                borderColor: '#004085',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
