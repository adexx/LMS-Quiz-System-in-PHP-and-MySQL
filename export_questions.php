<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Get all quizzes for selection
$quizzes = $pdo->query("SELECT id, title FROM quizzes ORDER BY title")->fetchAll();

if (isset($_GET['export']) && $_GET['quiz_id']) {
    $quiz_id = $_GET['quiz_id'];
    
    // Get quiz details
    $stmt = $pdo->prepare("SELECT title FROM quizzes WHERE id = ?");
    $stmt->execute([$quiz_id]);
    $quiz = $stmt->fetch();
    
    // Get questions
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll();
    
    if ($questions) {
        $filename = 'questions_' . preg_replace('/[^a-zA-Z0-9]/', '_', $quiz['title']) . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Answer']);
        
        // Data rows
        foreach ($questions as $question) {
            fputcsv($output, [
                $question['question_text'],
                $question['option_a'],
                $question['option_b'],
                $question['option_c'],
                $question['option_d'],
                $question['correct_answer']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Questions - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <h1 class="mb-4">Export Questions</h1>
                
                <div class="card">
                    <div class="card-body">
                        <form method="GET">
                            <input type="hidden" name="export" value="1">
                            
                            <div class="mb-3">
                                <label for="quiz_id" class="form-label">Select Quiz *</label>
                                <select class="form-control" id="quiz_id" name="quiz_id" required>
                                    <option value="">Choose a quiz to export...</option>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download"></i> Export to CSV
                            </button>
                            <a href="manage_quizzes.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Export Information</h5>
                    </div>
                    <div class="card-body">
                        <p>The exported CSV file will contain:</p>
                        <ul>
                            <li>All questions for the selected quiz</li>
                            <li>All answer options (A, B, C, D)</li>
                            <li>Correct answer indicators</li>
                            <li>Ready-to-import format</li>
                        </ul>
                        <p class="text-muted">
                            <small>The exported file can be modified and re-imported into the same or different quiz.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>