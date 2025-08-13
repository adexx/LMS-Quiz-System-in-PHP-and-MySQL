<?php
require_once 'config.php';
session_start();

if (!isset($_GET['quiz_id']) || !isset($_GET['user_name'])) {
    header('Location: index.php');
    exit;
}

$quiz_id = (int)$_GET['quiz_id'];
$user_name = $_GET['user_name'];

// Get quiz details with settings - make sure it's active
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_active = 1");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    echo "<div class='alert alert-danger'>Quiz not found or inactive. Quiz ID: $quiz_id</div>";
    echo "<a href='index.php'>Back to Home</a>";
    exit;
}

// Get all questions for THIS SPECIFIC quiz
$sql = "SELECT * FROM questions WHERE quiz_id = ?";
if ($quiz['shuffle_questions']) {
    $sql .= " ORDER BY RAND()";
} else {
    $sql .= " ORDER BY id";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// Debug: Check if questions were found
if (empty($questions)) {
    echo "<div class='alert alert-danger'>";
    echo "No questions found for quiz ID: $quiz_id<br>";
    echo "Quiz Title: " . htmlspecialchars($quiz['title']) . "<br>";
    echo "<a href='manage_questions.php?quiz_id=$quiz_id'>Add questions (Admin)</a>";
    echo "</div>";
    exit;
}

// Create quiz attempt - clear any existing session data first
if (!isset($_SESSION['attempt_id']) || $_SESSION['quiz_id'] != $quiz_id) {
    // Clear old session data
    unset($_SESSION['attempt_id']);
    unset($_SESSION['quiz_id']);
    unset($_SESSION['current_question']);
    unset($_SESSION['answers']);
    unset($_SESSION['questions']);
    
    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (quiz_id, user_name, total_questions, attempted_questions, correct_answers, score) VALUES (?, ?, ?, 0, 0, 0)");
    $stmt->execute([$quiz_id, $user_name, count($questions)]);
    $_SESSION['attempt_id'] = $pdo->lastInsertId();
    $_SESSION['quiz_id'] = $quiz_id;
    $_SESSION['current_question'] = 0;
    $_SESSION['answers'] = array_fill(0, count($questions), null);
    $_SESSION['questions'] = $questions;
    
    error_log("Created new quiz attempt with " . count($questions) . " questions for quiz ID: $quiz_id");
}

$current_question_index = $_SESSION['current_question'];
$current_question = isset($_SESSION['questions'][$current_question_index]) ? $_SESSION['questions'][$current_question_index] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taking Quiz: <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .timer.warning {
            background: #fd7e14;
            animation: pulse 1s infinite;
        }
        .timer.critical {
            background: #dc3545;
            animation: flash 0.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes flash {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .question-nav {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .progress-indicators {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        
        .progress-dot {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .progress-dot:hover:not(.disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .progress-dot.current {
            background: #007bff;
            color: white;
            border-color: #0056b3;
            transform: scale(1.1);
        }
        
        .progress-dot.answered {
            background: #28a745;
            color: white;
            border-color: #1e7e34;
        }
        
        .progress-dot.skipped {
            background: #ffc107;
            color: #000;
            border-color: #d39e00;
        }
        
        .progress-dot.unanswered {
            background: #e9ecef;
            color: #6c757d;
            border: 2px dashed #dee2e6;
        }
        
        .progress-dot.disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
        
        .question-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .question-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .form-check {
            margin: 15px 0;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .form-check:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .form-check-input:checked + .form-check-label {
            color: #007bff;
            font-weight: bold;
        }
        
        .nav-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #6c757d;
        }
        
        .legend-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="timer" id="timer"><?php echo $quiz['time_per_question']; ?>:00</div>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10">
                <?php if ($current_question): ?>
                    <div class="question-nav">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-1"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    Time per question: <?php echo $quiz['time_per_question']; ?> seconds
                                    <?php if ($quiz['allow_navigation']): ?>
                                        | <i class="fas fa-mouse-pointer"></i> Click numbers to navigate
                                    <?php endif; ?>
                                </small>
                            </div>
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                Question <?php echo $current_question_index + 1; ?> of <?php echo count($_SESSION['questions']); ?>
                            </span>
                        </div>
                        
                        <!-- Progress indicators -->
                        <div class="progress-indicators">
                            <?php for ($i = 0; $i < count($_SESSION['questions']); $i++): ?>
                                <?php 
                                $class = 'progress-dot ';
                                
                                if ($i == $current_question_index) {
                                    $class .= 'current';
                                } elseif (isset($_SESSION['answers'][$i])) {
                                    if ($_SESSION['answers'][$i] === 'SKIPPED') {
                                        $class .= 'skipped';
                                    } elseif ($_SESSION['answers'][$i] !== null) {
                                        $class .= 'answered';
                                    } else {
                                        $class .= 'unanswered';
                                    }
                                } else {
                                    $class .= 'unanswered';
                                }
                                
                                // Check if this question is clickable
                                $clickable = $quiz['allow_navigation'];
                                if (!$clickable) {
                                    $class .= ' disabled';
                                }
                                ?>
                                <div class="<?php echo $class; ?>" 
                                     title="Question <?php echo $i + 1; ?><?php echo $clickable ? ' (Click to navigate)' : ''; ?>"
                                     <?php if ($clickable): ?>
                                         onclick="navigateToQuestion(<?php echo $i; ?>)"
                                     <?php endif; ?>>
                                    <?php echo $i + 1; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                        
                        <!-- Navigation Legend -->
                        <div class="nav-legend">
                            <div class="legend-item">
                                <div class="legend-dot" style="background: #007bff;"></div>
                                <span>Current</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: #28a745;"></div>
                                <span>Answered</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: #ffc107;"></div>
                                <span>Skipped</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: #e9ecef; border: 2px dashed #dee2e6;"></div>
                                <span>Unanswered</span>
                            </div>
                        </div>
                        
                        <?php if (!$quiz['allow_navigation']): ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <small><i class="fas fa-info-circle"></i> Navigation between questions is disabled for this quiz.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card question-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>
                                Question <?php echo $current_question_index + 1; ?>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="questionForm" method="POST" action="process_answer.php">
                                <input type="hidden" name="question_id" value="<?php echo $current_question['id']; ?>">
                                <input type="hidden" name="attempt_id" value="<?php echo $_SESSION['attempt_id']; ?>">
                                <input type="hidden" name="question_index" value="<?php echo $current_question_index; ?>">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                                <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
                                
                                <div class="mb-4">
                                    <h6 class="fs-5"><?php echo htmlspecialchars($current_question['question_text']); ?></h6>
                                </div>

                                <div class="mb-4">
                                    <?php 
                                    $selected_answer = $_SESSION['answers'][$current_question_index] ?? null;
                                    if ($selected_answer === 'SKIPPED') $selected_answer = null;
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="option_a" value="a" <?php echo $selected_answer == 'a' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="option_a">
                                            <strong>A)</strong> <?php echo htmlspecialchars($current_question['option_a']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="option_b" value="b" <?php echo $selected_answer == 'b' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="option_b">
                                            <strong>B)</strong> <?php echo htmlspecialchars($current_question['option_b']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="option_c" value="c" <?php echo $selected_answer == 'c' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="option_c">
                                            <strong>C)</strong> <?php echo htmlspecialchars($current_question['option_c']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer" id="option_d" value="d" <?php echo $selected_answer == 'd' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="option_d">
                                            <strong>D)</strong> <?php echo htmlspecialchars($current_question['option_d']); ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($quiz['allow_skip']): ?>
                                            <button type="submit" name="action" value="skip" class="btn btn-warning">
                                                <i class="fas fa-forward"></i> Skip Question
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($quiz['allow_navigation'] && $current_question_index > 0): ?>
                                            <button type="submit" name="action" value="previous" class="btn btn-outline-secondary ms-2">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <button type="submit" name="action" value="save" class="btn btn-info me-2">
                                            <i class="fas fa-save"></i> Save Answer
                                        </button>
                                        
                                        <?php if ($current_question_index < count($_SESSION['questions']) - 1): ?>
                                            <button type="submit" name="action" value="next" class="btn btn-primary">
                                                Next Question <i class="fas fa-arrow-right"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="finish" class="btn btn-success">
                                                <i class="fas fa-check"></i> Finish Quiz
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Progress:</strong> 
                                        Answered: <?php echo count(array_filter($_SESSION['answers'], function($a) { return $a && $a !== 'SKIPPED'; })); ?> |
                                        Skipped: <?php echo count(array_filter($_SESSION['answers'], function($a) { return $a === 'SKIPPED'; })); ?> |
                                        Remaining: <?php echo count($_SESSION['questions']) - count(array_filter($_SESSION['answers'])); ?>
                                    </small>
                                </div>
                                
                                <a href="finish_quiz.php" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to submit the quiz now? Any unanswered questions will be marked as skipped.')">
                                    <i class="fas fa-stop"></i> Submit Quiz Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        <h4><i class="fas fa-check-circle"></i> Quiz Completed!</h4>
                        <p>You have reached the end of the quiz. Click below to submit your answers.</p>
                                                <a href="finish_quiz.php" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let timeLeft = <?php echo $quiz['time_per_question']; ?>;
        let timer = document.getElementById('timer');
        
        function updateTimer() {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timer.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            
            // Update timer styles based on remaining time
            timer.classList.remove('warning', 'critical');
            
            if (timeLeft <= 10) {
                timer.classList.add('critical');
            } else if (timeLeft <= 20) {
                timer.classList.add('warning');
            }
            
            if (timeLeft <= 0) {
                <?php if ($quiz['allow_skip']): ?>
                // Auto-skip when time runs out
                if (document.querySelector('input[name="action"][value="skip"]')) {
                    document.querySelector('input[name="action"][value="skip"]').click();
                }
                <?php else: ?>
                // Auto-submit when time runs out if skip is not allowed
                if (document.querySelector('input[name="action"][value="next"]')) {
                    document.querySelector('input[name="action"][value="next"]').click();
                } else {
                    document.querySelector('input[name="action"][value="finish"]').click();
                }
                <?php endif; ?>
            }
            
            timeLeft--;
        }
        
        let timerInterval = setInterval(updateTimer, 1000);
        
        // Reset timer when form is submitted
        document.getElementById('questionForm').addEventListener('submit', function() {
            clearInterval(timerInterval);
        });
        
        // Navigation function
        function navigateToQuestion(questionIndex) {
            <?php if ($quiz['allow_navigation']): ?>
            // Save current answer if any is selected
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (selectedAnswer) {
                // Create a form to save current answer and navigate
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_answer.php';
                form.style.display = 'none';
                
                // Copy all hidden fields
                const hiddenFields = document.querySelectorAll('input[type="hidden"]');
                hiddenFields.forEach(field => {
                    const newField = document.createElement('input');
                    newField.type = 'hidden';
                    newField.name = field.name;
                    newField.value = field.value;
                    form.appendChild(newField);
                });
                
                // Add selected answer
                const answerField = document.createElement('input');
                answerField.type = 'hidden';
                answerField.name = 'answer';
                answerField.value = selectedAnswer.value;
                form.appendChild(answerField);
                
                // Add navigation target
                const navField = document.createElement('input');
                navField.type = 'hidden';
                navField.name = 'goto_question';
                navField.value = questionIndex;
                form.appendChild(navField);
                
                // Add action
                const actionField = document.createElement('input');
                actionField.type = 'hidden';
                actionField.name = 'action';
                actionField.value = 'navigate';
                form.appendChild(actionField);
                
                document.body.appendChild(form);
                form.submit();
            } else {
                // Navigate without saving if no answer selected
                const form = document.getElementById('questionForm');
                const navField = document.createElement('input');
                navField.type = 'hidden';
                navField.name = 'goto_question';
                navField.value = questionIndex;
                form.appendChild(navField);
                
                const actionField = document.querySelector('input[name="action"]') || document.createElement('input');
                actionField.type = 'hidden';
                actionField.name = 'action';
                actionField.value = 'navigate';
                if (!actionField.parentNode) form.appendChild(actionField);
                
                form.submit();
            }
            <?php else: ?>
            alert('Navigation between questions is not allowed for this quiz.');
            <?php endif; ?>
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) return; // Don't interfere with browser shortcuts
            
            switch(event.key) {
                case '1':
                case 'a':
                case 'A':
                    document.getElementById('option_a').checked = true;
                    break;
                case '2':
                case 'b':
                case 'B':
                    document.getElementById('option_b').checked = true;
                    break;
                case '3':
                case 'c':
                case 'C':
                    document.getElementById('option_c').checked = true;
                    break;
                case '4':
                case 'd':
                case 'D':
                    document.getElementById('option_d').checked = true;
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (document.querySelector('input[name="answer"]:checked')) {
                        if (document.querySelector('input[name="action"][value="next"]')) {
                            document.querySelector('input[name="action"][value="next"]').click();
                        } else {
                            document.querySelector('input[name="action"][value="finish"]').click();
                        }
                    }
                    break;
                case ' ':
                case 'Spacebar':
                    event.preventDefault();
                    <?php if ($quiz['allow_skip']): ?>
                    if (document.querySelector('input[name="action"][value="skip"]')) {
                        document.querySelector('input[name="action"][value="skip"]').click();
                    }
                    <?php endif; ?>
                    break;
            }
        });
        
        // Show keyboard shortcuts hint
        setTimeout(function() {
            console.log('Keyboard shortcuts: 1/A, 2/B, 3/C, 4/D to select answers, Enter to proceed, Space to skip');
        }, 1000);
    </script>
</body>
</html>