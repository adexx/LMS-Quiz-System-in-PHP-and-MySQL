<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    header('Location: manage_quizzes.php');
    exit;
}

// Get quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    header('Location: manage_quizzes.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer, time_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $quiz_id,
                    $_POST['question_text'],
                    $_POST['option_a'],
                    $_POST['option_b'],
                    $_POST['option_c'],
                    $_POST['option_d'],
                    $_POST['correct_answer'],
                    !empty($_POST['time_limit']) ? $_POST['time_limit'] : null
                ]);
                $success = "Question created successfully!";
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE questions SET question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=?, time_limit=? WHERE id=?");
                $stmt->execute([
                    $_POST['question_text'],
                    $_POST['option_a'],
                    $_POST['option_b'],
                    $_POST['option_c'],
                    $_POST['option_d'],
                    $_POST['correct_answer'],
                    !empty($_POST['time_limit']) ? $_POST['time_limit'] : null,
                    $_POST['question_id']
                ]);
                $success = "Question updated successfully!";
                break;
                
            case 'delete':
                $pdo->prepare("DELETE FROM quiz_answers WHERE question_id = ?")->execute([$_POST['question_id']]);
                $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$_POST['question_id']]);
                $success = "Question deleted successfully!";
                break;
        }
    }
}

// Get all questions for this quiz
$questions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$questions->execute([$quiz_id]);
$questions = $questions->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Manage Questions</h1>
                        <p class="text-muted">Quiz: <?php echo htmlspecialchars($quiz['title']); ?></p>
                        <small class="text-info">
                            <i class="fas fa-info-circle"></i> 
                            Default time per question: <?php echo $quiz['time_per_question']; ?> seconds
                        </small>
                    </div>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                        <a href="manage_quizzes.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Quizzes
                        </a>
                    </div>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($questions)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No questions found for this quiz.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                                    Add First Question
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            Question <?php echo $index + 1; ?>
                                            <?php if ($question['time_limit']): ?>
                                                <span class="badge bg-info ms-2">
                                                    <i class="fas fa-clock"></i> <?php echo $question['time_limit']; ?>s
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary ms-2">
                                                    <i class="fas fa-clock"></i> Default (<?php echo $quiz['time_per_question']; ?>s)
                                                </span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary" onclick="editQuestion(<?php echo htmlspecialchars(json_encode($question)); ?>)" title="Edit Question">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteQuestion(<?php echo $question['id']; ?>)" title="Delete Question">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="fw-bold"><?php echo htmlspecialchars($question['question_text']); ?></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2 <?php echo $question['correct_answer'] == 'a' ? 'text-success fw-bold' : ''; ?>">
                                                    A) <?php echo htmlspecialchars($question['option_a']); ?>
                                                    <?php if ($question['correct_answer'] == 'a'): ?><i class="fas fa-check text-success"></i><?php endif; ?>
                                                </div>
                                                <div class="mb-2 <?php echo $question['correct_answer'] == 'b' ? 'text-success fw-bold' : ''; ?>">
                                                    B) <?php echo htmlspecialchars($question['option_b']); ?>
                                                    <?php if ($question['correct_answer'] == 'b'): ?><i class="fas fa-check text-success"></i><?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2 <?php echo $question['correct_answer'] == 'c' ? 'text-success fw-bold' : ''; ?>">
                                                    C) <?php echo htmlspecialchars($question['option_c']); ?>
                                                    <?php if ($question['correct_answer'] == 'c'): ?><i class="fas fa-check text-success"></i><?php endif; ?>
                                                </div>
                                                <div class="mb-2 <?php echo $question['correct_answer'] == 'd' ? 'text-success fw-bold' : ''; ?>">
                                                    D) <?php echo htmlspecialchars($question['option_d']); ?>
                                                    <?php if ($question['correct_answer'] == 'd'): ?><i class="fas fa-check text-success"></i><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Question Modal -->
    <div class="modal fade" id="createQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text *</label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="option_a" class="form-label">Option A *</label>
                                <input type="text" class="form-control" id="option_a" name="option_a" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_b" class="form-label">Option B *</label>
                                <input type="text" class="form-control" id="option_b" name="option_b" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="option_c" class="form-label">Option C *</label>
                                <input type="text" class="form-control" id="option_c" name="option_c" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_d" class="form-label">Option D *</label>
                                <input type="text" class="form-control" id="option_d" name="option_d" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer *</label>
                                <select class="form-control" id="correct_answer" name="correct_answer" required>
                                    <option value="">Select correct answer</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="time_limit" class="form-label">
                                    Time Limit (seconds)
                                    <small class="text-muted">(Leave blank for default: <?php echo $quiz['time_per_question']; ?>s)</small>
                                </label>
                                <input type="number" class="form-control" id="time_limit" name="time_limit" min="5" max="600" placeholder="<?php echo $quiz['time_per_question']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="question_id" id="edit_question_id">
                        
                        <div class="mb-3">
                            <label for="edit_question_text" class="form-label">Question Text *</label>
                            <textarea class="form-control" id="edit_question_text" name="question_text" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_option_a" class="form-label">Option A *</label>
                                <input type="text" class="form-control" id="edit_option_a" name="option_a" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_option_b" class="form-label">Option B *</label>
                                <input type="text" class="form-control" id="edit_option_b" name="option_b" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_option_c" class="form-label">Option C *</label>
                                <input type="text" class="form-control" id="edit_option_c" name="option_c" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_option_d" class="form-label">Option D *</label>
                                <input type="text" class="form-control" id="edit_option_d" name="option_d" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_correct_answer" class="form-label">Correct Answer *</label>
                                <select class="form-control" id="edit_correct_answer" name="correct_answer" required>
                                    <option value="">Select correct answer</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_time_limit" class="form-label">
                                    Time Limit (seconds)
                                    <small class="text-muted">(Leave blank for default: <?php echo $quiz['time_per_question']; ?>s)</small>
                                </label>
                                <input type="number" class="form-control" id="edit_time_limit" name="time_limit" min="5" max="600">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Question Modal -->
    <div class="modal fade" id="deleteQuestionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="question_id" id="delete_question_id">
                        <p>Are you sure you want to delete this question?</p>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This will also delete all student answers to this question. This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editQuestion(question) {
            document.getElementById('edit_question_id').value = question.id;
            document.getElementById('edit_question_text').value = question.question_text;
            document.getElementById('edit_option_a').value = question.option_a;
            document.getElementById('edit_option_b').value = question.option_b;
            document.getElementById('edit_option_c').value = question.option_c;
            document.getElementById('edit_option_d').value = question.option_d;
            document.getElementById('edit_correct_answer').value = question.correct_answer;
            document.getElementById('edit_time_limit').value = question.time_limit || '';
            
            
            new bootstrap.Modal(document.getElementById('editQuestionModal')).show();
        }
        
        function deleteQuestion(id) {
            document.getElementById('delete_question_id').value = id;
            new bootstrap.Modal(document.getElementById('deleteQuestionModal')).show();
        }
    </script>
</body>
</html>