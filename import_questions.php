<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Get all quizzes for selection
$quizzes = $pdo->query("SELECT id, title FROM quizzes ORDER BY title")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['import_file'])) {
    $quiz_id = $_POST['quiz_id'];
    $file = $_FILES['import_file'];
    
    if ($file['error'] == 0) {
        $filename = $file['tmp_name'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        $imported = 0;
        $errors = [];
        
        if ($extension == 'csv') {
            if (($handle = fopen($filename, "r")) !== FALSE) {
                // Skip header row
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 6) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([
                                $quiz_id,
                                trim($data[0]), // question_text
                                trim($data[1]), // option_a
                                trim($data[2]), // option_b
                                trim($data[3]), // option_c
                                trim($data[4]), // option_d
                                strtolower(trim($data[5])) // correct_answer
                            ]);
                            $imported++;
                        } catch (Exception $e) {
                            $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                        }
                    } else {
                        $errors[] = "Row " . ($imported + 1) . ": Insufficient data";
                    }
                }
                fclose($handle);
            }
        } else {
            $errors[] = "Only CSV files are supported";
        }
        
        if ($imported > 0) {
            $success = "$imported questions imported successfully!";
        }
    } else {
        $errors[] = "File upload error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Questions - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h1 class="mb-4">Import Questions</h1>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                                                <strong>Import Errors:</strong>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="quiz_id" class="form-label">Select Quiz *</label>
                                <select class="form-control" id="quiz_id" name="quiz_id" required>
                                    <option value="">Choose a quiz...</option>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="import_file" class="form-label">CSV File *</label>
                                <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv" required>
                                <div class="form-text">Upload a CSV file with questions</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Import Questions
                            </button>
                            <a href="manage_quizzes.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">CSV Format Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p>The CSV file should have the following columns in order:</p>
                        <ol>
                            <li><strong>Question Text</strong> - The question content</li>
                            <li><strong>Option A</strong> - First answer option</li>
                            <li><strong>Option B</strong> - Second answer option</li>
                            <li><strong>Option C</strong> - Third answer option</li>
                            <li><strong>Option D</strong> - Fourth answer option</li>
                            <li><strong>Correct Answer</strong> - Letter (a, b, c, or d)</li>
                        </ol>
                        
                        <h6>Sample CSV Format:</h6>
                        <pre class="bg-light p-3">
Question Text,Option A,Option B,Option C,Option D,Correct Answer
"What does PHP stand for?","Personal Home Page","PHP: Hypertext Preprocessor","Private Home Page","Programming Home Page","b"
"Which symbol is used for variables in PHP?","@","#","$","%","c"
                        </pre>
                        
                        <div class="mt-3">
                            <a href="download_sample.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Download Sample CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>