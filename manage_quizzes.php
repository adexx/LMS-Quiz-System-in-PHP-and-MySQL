<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
    $stmt = $pdo->prepare("INSERT INTO quizzes (title, description, time_per_question, allow_skip, allow_navigation, shuffle_questions, show_results, pass_percentage, attempts_allowed, is_active, questions_to_show, allow_question_selection) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['time_per_question'],
        isset($_POST['allow_skip']) ? 1 : 0,
        isset($_POST['allow_navigation']) ? 1 : 0,
        isset($_POST['shuffle_questions']) ? 1 : 0,
        isset($_POST['show_results']) ? 1 : 0,
        $_POST['pass_percentage'],
        $_POST['attempts_allowed'],
        isset($_POST['is_active']) ? 1 : 0,
        !empty($_POST['questions_to_show']) ? $_POST['questions_to_show'] : null,
        isset($_POST['allow_question_selection']) ? 1 : 0
    ]);
    break;

case 'update':
    $stmt = $pdo->prepare("UPDATE quizzes SET title=?, description=?, time_per_question=?, allow_skip=?, allow_navigation=?, shuffle_questions=?, show_results=?, pass_percentage=?, attempts_allowed=?, is_active=?, questions_to_show=?, allow_question_selection=? WHERE id=?");
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['time_per_question'],
        isset($_POST['allow_skip']) ? 1 : 0,
        isset($_POST['allow_navigation']) ? 1 : 0,
        isset($_POST['shuffle_questions']) ? 1 : 0,
        isset($_POST['show_results']) ? 1 : 0,
        $_POST['pass_percentage'],
        $_POST['attempts_allowed'],
        isset($_POST['is_active']) ? 1 : 0,
        !empty($_POST['questions_to_show']) ? $_POST['questions_to_show'] : null,
        isset($_POST['allow_question_selection']) ? 1 : 0,
        $_POST['quiz_id']
    ]);
    break;
                
            case 'delete':
                // Delete quiz and related data
                $pdo->prepare("DELETE FROM quiz_answers WHERE attempt_id IN (SELECT id FROM quiz_attempts WHERE quiz_id = ?)")->execute([$_POST['quiz_id']]);
                $pdo->prepare("DELETE FROM quiz_attempts WHERE quiz_id = ?")->execute([$_POST['quiz_id']]);
                $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$_POST['quiz_id']]);
                $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$_POST['quiz_id']]);
                $success = "Quiz deleted successfully!";
                break;
        }
    }
}

// Get all quizzes
$quizzes = $pdo->query("SELECT q.*, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as question_count FROM quizzes q ORDER BY q.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quizzes - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Manage Quizzes</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuizModal">
                        <i class="fas fa-plus"></i> Create New Quiz
                    </button>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Questions</th>
                                        <th>Time/Question</th>
                                        <th>Settings</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <tr>
                                            <td><?php echo $quiz['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($quiz['title']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($quiz['description'], 0, 50)) . (strlen($quiz['description']) > 50 ? '...' : ''); ?></small>
                                            </td>
                                            <td><?php echo $quiz['question_count']; ?></td>
                                            <td><?php echo $quiz['time_per_question']; ?>s</td>
                                            <td>
                                                <small>
                                                    <?php echo $quiz['allow_skip'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?> Skip<br>
                                                    <?php echo $quiz['allow_navigation'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?> Navigation<br>
                                                    <?php echo $quiz['shuffle_questions'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'; ?> Shuffle
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $quiz['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $quiz['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-outline-primary" title="Manage Questions">
                                                        <i class="fas fa-question-circle"></i>
                                                    </a>
                                                    <button class="btn btn-outline-secondary" onclick="editQuiz(<?php echo htmlspecialchars(json_encode($quiz)); ?>)" title="Edit Quiz">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteQuiz(<?php echo $quiz['id']; ?>, '<?php echo htmlspecialchars($quiz['title']); ?>')" title="Delete Quiz">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Quiz Modal -->
    <div class="modal fade" id="createQuizModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Quiz</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Quiz Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="time_per_question" class="form-label">Time per Question (seconds) *</label>
                                <input type="number" class="form-control" id="time_per_question" name="time_per_question" value="60" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pass_percentage" class="form-label">Pass Percentage</label>
                                <input type="number" class="form-control" id="pass_percentage" name="pass_percentage" value="60" min="0" max="100" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attempts_allowed" class="form-label">Attempts Allowed</label>
                                <input type="number" class="form-control" id="attempts_allowed" name="attempts_allowed" value="1" min="1">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="allow_skip" name="allow_skip" checked>
                                    <label class="form-check-label" for="allow_skip">Allow Skip Questions</label>
                                </div>
                                <div class="row">
    <div class="col-md-6 mb-3">
        <label for="questions_to_show" class="form-label">
            Default Questions to Show
            <small class="text-muted">(Leave blank to show all questions)</small>
        </label>
        <input type="number" class="form-control" id="questions_to_show" name="questions_to_show" min="1" placeholder="All questions">
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="allow_question_selection" name="allow_question_selection">
            <label class="form-check-label" for="allow_question_selection">
                Allow Users to Choose Question Count
            </label>
        </div>
    </div>
</div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="allow_navigation" name="allow_navigation">
                                    <label class="form-check-label" for="allow_navigation">Allow Question Navigation</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions">
                                    <label class="form-check-label" for="shuffle_questions">Shuffle Questions</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_results" name="show_results" checked>
                                    <label class="form-check-label" for="show_results">Show Results to Students</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Quiz Modal -->
    <div class="modal fade" id="editQuizModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Quiz</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="quiz_id" id="edit_quiz_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_title" class="form-label">Quiz Title *</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_time_per_question" class="form-label">Time per Question (seconds) *</label>
                                                                <input type="number" class="form-control" id="edit_time_per_question" name="time_per_question" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_pass_percentage" class="form-label">Pass Percentage</label>
                                <input type="number" class="form-control" id="edit_pass_percentage" name="pass_percentage" min="0" max="100" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_attempts_allowed" class="form-label">Attempts Allowed</label>
                                <input type="number" class="form-control" id="edit_attempts_allowed" name="attempts_allowed" min="1">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_allow_skip" name="allow_skip">
                                    <label class="form-check-label" for="edit_allow_skip">Allow Skip Questions</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_allow_navigation" name="allow_navigation">
                                    <label class="form-check-label" for="edit_allow_navigation">Allow Question Navigation</label>
                                </div>
                                <div class="row">
    <div class="col-md-6 mb-3">
        <label for="questions_to_show" class="form-label">
            Default Questions to Show
            <small class="text-muted">(Leave blank to show all questions)</small>
        </label>
        <input type="number" class="form-control" id="questions_to_show" name="questions_to_show" min="1" placeholder="All questions">
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="allow_question_selection" name="allow_question_selection">
            <label class="form-check-label" for="allow_question_selection">
                Allow Users to Choose Question Count
            </label>
        </div>
    </div>
</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_shuffle_questions" name="shuffle_questions">
                                    <label class="form-check-label" for="edit_shuffle_questions">Shuffle Questions</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_show_results" name="show_results">
                                    <label class="form-check-label" for="edit_show_results">Show Results to Students</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                    <label class="form-check-label" for="edit_is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteQuizModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="quiz_id" id="delete_quiz_id">
                        <p>Are you sure you want to delete the quiz "<span id="delete_quiz_title"></span>"?</p>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This will also delete all questions and student attempts for this quiz. This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editQuiz(quiz) {
            document.getElementById('edit_quiz_id').value = quiz.id;
            document.getElementById('edit_title').value = quiz.title;
            document.getElementById('edit_description').value = quiz.description || '';
            document.getElementById('edit_time_per_question').value = quiz.time_per_question;
            document.getElementById('edit_pass_percentage').value = quiz.pass_percentage;
            document.getElementById('edit_attempts_allowed').value = quiz.attempts_allowed;
            
            document.getElementById('edit_allow_skip').checked = quiz.allow_skip == 1;
            document.getElementById('edit_allow_navigation').checked = quiz.allow_navigation == 1;
            document.getElementById('edit_shuffle_questions').checked = quiz.shuffle_questions == 1;
            document.getElementById('edit_show_results').checked = quiz.show_results == 1;
            document.getElementById('edit_is_active').checked = quiz.is_active == 1;
            document.getElementById('edit_allow_navigation').checked = quiz.allow_navigation == 1;
            
            new bootstrap.Modal(document.getElementById('editQuizModal')).show();
            document.getElementById('edit_questions_to_show').value = quiz.questions_to_show || '';
    document.getElementById('edit_allow_question_selection').checked = quiz.allow_question_selection == 1;
        }
        
        function deleteQuiz(id, title) {
            document.getElementById('delete_quiz_id').value = id;
            document.getElementById('delete_quiz_title').textContent = title;
            new bootstrap.Modal(document.getElementById('deleteQuizModal')).show();
        }
    </script>
</body>
</html>