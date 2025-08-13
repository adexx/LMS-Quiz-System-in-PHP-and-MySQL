<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['attempt_id'])) {
    header('Location: index.php');
    exit;
}

$attempt_id = $_SESSION['attempt_id'];

// Process any remaining answers
if (isset($_SESSION['questions']) && isset($_SESSION['answers'])) {
    $correct_count = 0;
    $attempted_count = 0;
    
    // Process each question
    for ($i = 0; $i < count($_SESSION['questions']); $i++) {
        $question = $_SESSION['questions'][$i];
        $selected_answer = $_SESSION['answers'][$i] ?? 'SKIPPED';
        
        $is_correct = false;
        $answer_to_store = null;
        
        if ($selected_answer && $selected_answer !== 'SKIPPED') {
            $attempted_count++;
            $is_correct = ($selected_answer === $question['correct_answer']);
            if ($is_correct) $correct_count++;
            $answer_to_store = $selected_answer;
        }
        
        // Save individual answer to database
        $stmt = $pdo->prepare("
            INSERT INTO quiz_answers (attempt_id, question_id, selected_answer, is_correct, time_taken) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            selected_answer = VALUES(selected_answer), 
            is_correct = VALUES(is_correct)
        ");
        $stmt->execute([
            $attempt_id, 
            $question['id'], 
            $answer_to_store, 
            $is_correct ? 1 : 0, 
            30
        ]);
    }
    
    // Update attempt with final counts and completion time
    $total_questions = count($_SESSION['questions']);
    $score = $total_questions > 0 ? ($correct_count / $total_questions) * 100 : 0;
    
    $stmt = $pdo->prepare("UPDATE quiz_attempts SET attempted_questions = ?, correct_answers = ?, completed_at = NOW(), score = ? WHERE id = ?");
    $stmt->execute([$attempted_count, $correct_count, $score, $attempt_id]);
}

// Get quiz details for display
$stmt = $pdo->prepare("SELECT q.title, qa.* FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.id WHERE qa.id = ?");
$stmt->execute([$attempt_id]);
$result = $stmt->fetch();

// Store attempt_id for the detailed results link
$completed_attempt_id = $attempt_id;

// Clear session
unset($_SESSION['attempt_id']);
unset($_SESSION['quiz_id']);
unset($_SESSION['current_question']);
unset($_SESSION['answers']);
unset($_SESSION['questions']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .result-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card result-card">
                    <div class="card-body text-center p-5">
                        <h2 class="mb-4">
                            <i class="fas fa-trophy"></i> Quiz Completed!
                        </h2>
                        
                        <div class="score-circle">
                            <div>
                                <h1 class="mb-0"><?php echo number_format($result['score'], 1); ?>%</h1>
                                <small>Final Score</small>
                            </div>
                        </div>
                        
                        <h4 class="mb-2"><?php echo htmlspecialchars($result['title']); ?></h4>
                        <p class="mb-4"><strong>Student:</strong> <?php echo htmlspecialchars($result['user_name']); ?></p>
                        
                       // In the stats display section, replace with:
<div class="stats-grid">
    <div class="stat-item">
        <h3 class="mb-1"><?php echo $result['correct_answers']; ?></h3>
        <small>Correct Answers</small>
    </div>
    <div class="stat-item">
        <h3 class="mb-1"><?php echo $result['attempted_questions']; ?></h3>
        <small>Questions Attempted</small>
    </div>
    <div class="stat-item">
        <h3 class="mb-1"><?php echo ($result['selected_questions_count'] ?: $result['total_questions']) - $result['attempted_questions']; ?></h3>
        <small>Questions Skipped</small>
    </div>
    <div class="stat-item">
        <h3 class="mb-1"><?php echo $result['selected_questions_count'] ?: $result['total_questions']; ?></h3>
        <small>Total Selected</small>
    </div>
</div>

<?php if ($result['selected_questions_count'] && $result['selected_questions_count'] < $result['total_questions']): ?>
    <div class="mt-3">
        <small class="text-light">
            <i class="fas fa-info-circle"></i>
            You attempted <?php echo $result['selected_questions_count']; ?> randomly selected questions 
            from a bank of <?php echo $result['total_questions']; ?> available questions.
        </small>
    </div>
<?php endif; ?>
                        
                        <div class="mt-4">
                            <?php
                            $grade = '';
                            $grade_class = '';
                            if ($result['score'] >= 90) {
                                $grade = 'Excellent!';
                                $grade_class = 'success';
                            } elseif ($result['score'] >= 80) {
                                $grade = 'Very Good!';
                                $grade_class = 'info';
                            } elseif ($result['score'] >= 70) {
                                $grade = 'Good!';
                                $grade_class = 'primary';
                            } elseif ($result['score'] >= 60) {
                                $grade = 'Fair';
                                $grade_class = 'warning';
                            } else {
                                $grade = 'Needs Improvement';
                                $grade_class = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?php echo $grade_class; ?> fs-5 px-4 py-2">
                                <?php echo $grade; ?>
                            </span>
                        </div>
                        
                        <div class="mt-5">
                            <a href="result_details.php?attempt_id=<?php echo $completed_attempt_id; ?>" class="btn btn-light btn-lg me-3">
                                <i class="fas fa-chart-line"></i> View Detailed Results
                            </a>
                            <a href="index.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-redo"></i> Take Another Quiz
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <a href="results.php" class="btn btn-outline-light">
                                <i class="fas fa-history"></i> View All My Results
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Analysis -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Performance Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="performanceChart" width="300" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6>Summary:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> <strong><?php echo $result['correct_answers']; ?></strong> questions answered correctly</li>
                                    <li><i class="fas fa-times text-danger"></i> <strong><?php echo $result['attempted_questions'] - $result['correct_answers']; ?></strong> questions answered incorrectly</li>
                                    <li><i class="fas fa-forward text-warning"></i> <strong><?php echo $result['total_questions'] - $result['attempted_questions']; ?></strong> questions skipped</li>
                                </ul>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        <?php if ($result['score'] >= 70): ?>
                                            Congratulations! You've passed this quiz.
                                        <?php else: ?>
                                            Consider reviewing the material and trying again.
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Create performance chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Incorrect', 'Skipped'],
                datasets: [{
                    data: [
                        <?php echo $result['correct_answers']; ?>,
                        <?php echo $result['attempted_questions'] - $result['correct_answers']; ?>,
                        <?php echo $result['total_questions'] - $result['attempted_questions']; ?>
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#dc3545',
                        '#ffc107'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>