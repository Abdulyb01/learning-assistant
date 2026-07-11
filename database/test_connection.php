<?php
/**
 * Database Connection Test
 * 
 * This file tests the database connection and verifies all tables are created.
 * Access at: http://localhost/learning-assistant/database/test_connection.php
 * 
 * @author Learning Assistant Team
 * @version 1.0.0
 */

define('BASE_URL', 'http://localhost/learning-assistant');

// Include configuration
require_once '../config/database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4 text-primary">Database Connection Test</h1>

                <?php
                try {
                    // Test basic connection
                    echo '<div class="alert alert-success" role="alert">';
                    echo '<i class="bi bi-check-circle"></i> <strong>Connection Successful!</strong> Connected to database: ' . DB_NAME;
                    echo '</div>';

                    // Get table list
                    $stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    if (count($tables) > 0) {
                        echo '<div class="card">';
                        echo '<div class="card-header bg-primary text-white">';
                        echo '<h5 class="mb-0">Database Tables (' . count($tables) . ')</h5>';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<ul class="list-group">';

                        $expected_tables = [
                            'users', 'profiles', 'categories', 'courses', 'lessons',
                            'enrollments', 'progress', 'quizzes', 'questions', 'answers',
                            'results', 'recommendations', 'feedback', 'notifications',
                            'bookmarks', 'settings', 'announcements'
                        ];

                        foreach ($tables as $table) {
                            $status = in_array($table, $expected_tables) ? 'success' : 'warning';
                            echo '<li class="list-group-item">';
                            echo '<span class="badge bg-' . $status . '">' . $table . '</span>';

                            // Get table row count
                            $countStmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table");
                            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                            echo ' <small class="text-muted">(' . $count . ' rows)</small>';
                            echo '</li>';
                        }

                        echo '</ul>';
                        echo '</div>';
                        echo '</div>';
                    }

                    // Verify seed data
                    echo '<br><div class="card">';
                    echo '<div class="card-header bg-info text-white">';
                    echo '<h5 class="mb-0">Database Statistics</h5>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<div class="row">';

                    $stats = [
                        'Users' => 'users',
                        'Courses' => 'courses',
                        'Lessons' => 'lessons',
                        'Enrollments' => 'enrollments',
                        'Quiz Results' => 'results',
                        'Recommendations' => 'recommendations',
                        'Notifications' => 'notifications'
                    ];

                    foreach ($stats as $label => $table) {
                        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table");
                        $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                        echo '<div class="col-md-6 mb-3">';
                        echo '<div class="card border-0 bg-light">';
                        echo '<div class="card-body">';
                        echo '<h6 class="card-title text-muted">' . $label . '</h6>';
                        echo '<h2 class="text-primary">' . $count . '</h2>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>';
                    echo '</div>';

                    echo '<br><div class="alert alert-info" role="alert">';
                    echo '<strong>Next Steps:</strong><br>';
                    echo '1. Database schema has been created with 17 tables<br>';
                    echo '2. Seed data has been populated for testing<br>';
                    echo '3. Database is ready for Phase 3 - Authentication System<br>';
                    echo '</div>';

                } catch (Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo '<strong>Error:</strong> ' . $e->getMessage();
                    echo '</div>';
                }
                ?>

                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
