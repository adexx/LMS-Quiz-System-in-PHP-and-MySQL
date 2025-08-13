<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Get statistics
$stats = [];
$stats['total_quizzes'] = $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn();
$stats['total_questions'] = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$stats['total_attempts'] = $pdo->query("SELECT COUNT(*) FROM quiz_attempts WHERE completed_at IS NOT NULL")->fetchColumn();
$stats['active_quizzes'] = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE is_active = 1")->fetchColumn();

// Get recent attempts
$recent_attempts = $pdo->query("
    SELECT qa.*, q.title as quiz_title 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.completed_at IS NOT NULL 
    ORDER BY qa.completed_at DESC 
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LMS Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Dashboard</h1>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['total_quizzes']; ?></h4>
                                        <p class="mb-0">Total Quizzes</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clipboard-list fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['active_quizzes']; ?></h4>
                                        <p class="mb-0">Active Quizzes</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-play-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['total_questions']; ?></h4>
                                        <p class="mb-0">Total Questions</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-question-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['total_attempts']; ?></h4>
                                        <p class="mb-0">Quiz Attempts</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Quiz Attempts</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_attempts)): ?>
                                    <p class="text-muted">No recent attempts found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Quiz</th>
                                                    <th>Score</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_attempts as $attempt): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($attempt['user_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $attempt['score'] >= 70 ? 'success' : ($attempt['score'] >= 50 ? 'warning' : 'danger'); ?>">
                                                                <?php echo number_format($attempt['score'], 1); ?>%
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M j, Y', strtotime($attempt['completed_at'])); ?></td>
                                                        <td>
                                                            <a href="result_details.php?attempt_id=<?php echo $attempt['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>