<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Get filter parameters
$quiz_filter = $_GET['quiz_filter'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$where_conditions = ['qa.completed_at IS NOT NULL'];
$params = [];

if ($quiz_filter) {
    $where_conditions[] = 'qa.quiz_id = ?';
    $params[] = $quiz_filter;
}

if ($date_from) {
    $where_conditions[] = 'DATE(qa.completed_at) >= ?';
    $params[] = $date_from;
}

if ($date_to) {
    $where_conditions[] = 'DATE(qa.completed_at) <= ?';
    $params[] = $date_to;
}

$where_clause = implode(' AND ', $where_conditions);

// Get results
$stmt = $pdo->prepare("
    SELECT qa.*, q.title as quiz_title 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE $where_clause
    ORDER BY qa.completed_at DESC
");
$stmt->execute($params);
$results = $stmt->fetchAll();

// Get quizzes for filter dropdown
$quizzes = $pdo->query("SELECT id, title FROM quizzes ORDER BY title")->fetchAll();

// Calculate statistics
$total_attempts = count($results);
$passed_attempts = array_filter($results, function($r) { return $r['score'] >= 70; });
$pass_rate = $total_attempts > 0 ? (count($passed_attempts) / $total_attempts) * 100 : 0;
$avg_score = $total_attempts > 0 ? array_sum(array_column($results, 'score')) / $total_attempts : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Quiz Results Management</h1>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_attempts; ?></h4>
                                        <p class="mb-0">Total Attempts</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clipboard-check fa-2x"></i>
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
                                        <h4><?php echo number_format($pass_rate, 1); ?>%</h4>
                                        <p class="mb-0">Pass Rate</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-trophy fa-2x"></i>
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
                                        <h4><?php echo number_format($avg_score, 1); ?>%</h4>
                                        <p class="mb-0">Average Score</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-chart-line fa-2x"></i>
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
                                        <h4><?php echo count($passed_attempts); ?></h4>
                                        <p class="mb-0">Passed</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row align-items-end">
                            <div class="col-md-3">
                                <label for="quiz_filter" class="form-label">Filter by Quiz</label>
                                <select class="form-control" id="quiz_filter" name="quiz_filter">
                                    <option value="">All Quizzes</option>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <option value="<?php echo $quiz['id']; ?>" <?php echo $quiz_filter == $quiz['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($quiz['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="admin_results.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Results Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($results)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No quiz results found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Quiz</th>
                                            <th>Score</th>
                                            <th>Questions</th>
                                            <th>Correct</th>
                                            <th>Completion Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $result): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($result['user_name']); ?></td>
                                                <td><?php echo htmlspecialchars($result['quiz_title']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $result['score'] >= 70 ? 'success' : ($result['score'] >= 50 ? 'warning' : 'danger'); ?>">
                                                        <?php echo number_format($result['score'], 1); ?>%
                                                    </span>
                                                </td>
                                                <td><?php echo $result['attempted_questions']; ?>/<?php echo $result['total_questions']; ?></td>
                                                <td><?php echo $result['correct_answers']; ?></td>
                                                <td><?php echo date('M j, Y g:i A', strtotime($result['completed_at'])); ?></td>
                                                <td>
                                                    <a href="result_details.php?attempt_id=<?php echo $result['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>