<?php
require_once 'config.php';

// Get all quiz attempts
$stmt = $pdo->query("
    SELECT qa.*, q.title as quiz_title 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.completed_at IS NOT NULL 
    ORDER BY qa.completed_at DESC
");
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">All Quiz Results</h1>
        
        <?php if (empty($results)): ?>
            <div class="alert alert-info">No quiz results available.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>Questions Attempted</th>
                            <th>Correct Answers</th>
                            <th>Completion Date</th>
                            <th>Action</th>
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
                                    <a href="result_details.php?attempt_id=<?php echo $result['id']; ?>" class="btn btn-sm btn-info">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Back to Quizzes</a>
        </div>
    </div>
</body>
</html>