<?php
require_once 'config.php';

if (!isset($_GET['attempt_id'])) {
    header('Location: results.php');
    exit;
}

$attempt_id = $_GET['attempt_id'];

// Get attempt details
$stmt = $pdo->prepare("
    SELECT qa.*, q.title as quiz_title, q.description
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.id = ?
");
$stmt->execute([$attempt_id]);
$attempt = $stmt->fetch();

if (!$attempt) {
    header('Location: results.php');
    exit;
}

// Get detailed answers for this attempt
$stmt = $pdo->prepare("
    SELECT 
        qans.*,
        quest.question_text,
        quest.option_a,
        quest.option_b,
        quest.option_c,
        quest.option_d,
        quest.correct_answer
    FROM quiz_answers qans
    JOIN questions quest ON qans.question_id = quest.id
    WHERE qans.attempt_id = ?
    ORDER BY quest.id
");
$stmt->execute([$attempt_id]);
$answers = $stmt->fetchAll();

function getOptionText($question, $option) {
    if (!$option) return 'Not Answered';
    
    switch($option) {
        case 'a': return $question['option_a'];
        case 'b': return $question['option_b'];
        case 'c': return $question['option_c'];
        case 'd': return $question['option_d'];
        default: return 'Not Answered';
    }
}

function getOptionLabel($option) {
    if (!$option) return 'N/A';
    
    switch($option) {
        case 'a': return 'A';
        case 'b': return 'B';
        case 'c': return 'C';
        case 'd': return 'D';
        default: return 'N/A';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Results - <?php echo htmlspecialchars($attempt['quiz_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .question-card {
            margin-bottom: 20px;
            border-left: 4px solid #dee2e6;
        }
        .question-card.correct {
            border-left-color: #28a745;
            background-color: #f8fff9;
        }
        .question-card.incorrect {
            border-left-color: #dc3545;
            background-color: #fff8f8;
        }
        .question-card.skipped {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
        .answer-option {
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .answer-option.selected {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .answer-option.correct {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        .answer-option.selected.incorrect {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Header with overall results -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1"><?php echo htmlspecialchars($attempt['quiz_title']); ?></h2>
                                <p class="mb-1"><strong>Student:</strong> <?php echo htmlspecialchars($attempt['user_name']); ?></p>
                                <p class="mb-0"><small>Completed: <?php echo date('F j, Y \a\t g:i A', strtotime($attempt['completed_at'])); ?></small></p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h1 class="mb-0"><?php echo number_format($attempt['score'], 1); ?>%</h1>
                                <p class="mb-0">Final Score</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success"><?php echo $attempt['correct_answers']; ?></h4>
                        <small class="text-muted">Correct</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-danger"><?php echo $attempt['attempted_questions'] - $attempt['correct_answers']; ?></h4>
                        <small class="text-muted">Incorrect</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning"><?php echo $attempt['total_questions'] - $attempt['attempted_questions']; ?></h4>
                        <small class="text-muted">Skipped</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info"><?php echo $attempt['total_questions']; ?></h4>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question by Question Results -->
        <h3 class="mb-3">Question by Question Results</h3>
        
        <?php if (empty($answers)): ?>
            <div class="alert alert-info">No detailed answers found for this attempt.</div>
        <?php else: ?>
            <?php $questionNumber = 1; ?>
            <?php foreach ($answers as $answer): ?>
                <?php 
                $cardClass = 'question-card ';
                $statusIcon = '';
                $statusText = '';
                
                if ($answer['selected_answer'] === null) {
                    $cardClass .= 'skipped';
                    $statusIcon = '⚠️';
                    $statusText = 'Skipped';
                } elseif ($answer['is_correct']) {
                    $cardClass .= 'correct';
                    $statusIcon = '✅';
                    $statusText = 'Correct';
                } else {
                    $cardClass .= 'incorrect';
                    $statusIcon = '❌';
                    $statusText = 'Incorrect';
                }
                ?>
                
                <div class="card <?php echo $cardClass; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Question <?php echo $questionNumber; ?></h5>
                        <span class="badge bg-<?php echo $answer['selected_answer'] === null ? 'warning' : ($answer['is_correct'] ? 'success' : 'danger'); ?>">
                            <?php echo $statusIcon . ' ' . $statusText; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Answer Options:</h6>
                                <?php foreach (['a', 'b', 'c', 'd'] as $option): ?>
                                    <?php 
                                    $optionClass = 'answer-option';
                                    if ($option == $answer['selected_answer'] && $option == $answer['correct_answer']) {
                                        $optionClass .= ' selected correct';
                                    } elseif ($option == $answer['selected_answer']) {
                                        $optionClass .= ' selected incorrect';
                                    } elseif ($option == $answer['correct_answer']) {
                                        $optionClass .= ' correct';
                                    }
                                    ?>
                                    <div class="<?php echo $optionClass; ?>">
                                        <strong><?php echo strtoupper($option); ?>)</strong> <?php echo htmlspecialchars(getOptionText($answer, $option)); ?>
                                        <?php if ($option == $answer['correct_answer']): ?>
                                            <i class="fas fa-check text-success ms-2" title="Correct Answer"></i>
                                        <?php endif; ?>
                                        <?php if ($option == $answer['selected_answer']): ?>
                                            <i class="fas fa-hand-point-right ms-2" title="Your Answer"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <h6>Result Summary:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Your Answer:</strong> 
                                        <?php if ($answer['selected_answer']): ?>
                                            <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                                                <?php echo getOptionLabel($answer['selected_answer']); ?>) <?php echo htmlspecialchars(getOptionText($answer, $answer['selected_answer'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Not Answered / Skipped</span>
                                        <?php endif; ?>
                                    </li>
                                                                        <li><strong>Correct Answer:</strong> 
                                        <span class="badge bg-success">
                                            <?php echo getOptionLabel($answer['correct_answer']); ?>) <?php echo htmlspecialchars(getOptionText($answer, $answer['correct_answer'])); ?>
                                        </span>
                                    </li>
                                    <li><strong>Result:</strong> 
                                        <?php if ($answer['selected_answer'] === null): ?>
                                            <span class="badge bg-warning text-dark">Skipped</span>
                                        <?php elseif ($answer['is_correct']): ?>
                                            <span class="badge bg-success">Correct ✓</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Incorrect ✗</span>
                                        <?php endif; ?>
                                    </li>
                                    <li><strong>Time Taken:</strong> <?php echo $answer['time_taken']; ?> seconds</li>
                                </ul>
                                
                                <?php if (!$answer['is_correct'] && $answer['selected_answer'] !== null): ?>
                                    <div class="alert alert-info mt-3">
                                        <small><i class="fas fa-lightbulb"></i> <strong>Learning Tip:</strong> Review the correct answer and understand why it's right.</small>
                                    </div>
                                <?php elseif ($answer['selected_answer'] === null): ?>
                                    <div class="alert alert-warning mt-3">
                                        <small><i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> This question was skipped. Consider reviewing the topic.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $questionNumber++; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Navigation Buttons -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="results.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Results
                </a>
                <a href="index.php" class="btn btn-primary me-2">
                    <i class="fas fa-home"></i> Take Another Quiz
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fas fa-print"></i> Print Results
                </button>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style media="print">
        .btn, .navbar, .fixed-top {
            display: none !important;
        }
        .stats-card {
            background: #f8f9fa !important;
            color: #000 !important;
        }
        .question-card {
            break-inside: avoid;
            margin-bottom: 15px;
        }
        .container {
            max-width: 100% !important;
        }
        body {
            font-size: 12px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>