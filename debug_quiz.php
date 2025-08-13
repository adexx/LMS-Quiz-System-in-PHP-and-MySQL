<?php
require_once 'config.php';

echo "<h2>Quiz Debug Information</h2>";

// Get all quizzes
echo "<h3>All Quizzes:</h3>";
$quizzes = $pdo->query("SELECT * FROM quizzes ORDER BY id")->fetchAll();
foreach ($quizzes as $quiz) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
    echo "<strong>Quiz ID:</strong> " . $quiz['id'] . "<br>";
    echo "<strong>Title:</strong> " . $quiz['title'] . "<br>";
    echo "<strong>Active:</strong> " . ($quiz['is_active'] ? 'Yes' : 'No') . "<br>";
    
    // Count questions for this quiz
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quiz['id']]);
    $question_count = $stmt->fetchColumn();
    echo "<strong>Question Count:</strong> " . $question_count . "<br>";
    
    // Show first few questions
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? LIMIT 3");
    $stmt->execute([$quiz['id']]);
    $questions = $stmt->fetchAll();
    
    echo "<strong>Sample Questions:</strong><br>";
    foreach ($questions as $q) {
        echo "- " . substr($q['question_text'], 0, 50) . "...<br>";
    }
    echo "</div>";
}

echo "<hr>";

// Check for orphaned questions (questions with invalid quiz_id)
echo "<h3>Questions without valid quiz:</h3>";
$orphaned = $pdo->query("
    SELECT q.*, qz.title as quiz_title 
    FROM questions q 
    LEFT JOIN quizzes qz ON q.quiz_id = qz.id 
    WHERE qz.id IS NULL
")->fetchAll();

if (empty($orphaned)) {
    echo "<p style='color: green;'>No orphaned questions found.</p>";
} else {
    foreach ($orphaned as $q) {
        echo "<p style='color: red;'>Question ID " . $q['id'] . " has invalid quiz_id: " . $q['quiz_id'] . "</p>";
    }
}

// Show URL parameters being used
echo "<hr>";
echo "<h3>Current URL Parameters:</h3>";
echo "<strong>quiz_id:</strong> " . ($_GET['quiz_id'] ?? 'Not set') . "<br>";
echo "<strong>user_name:</strong> " . ($_GET['user_name'] ?? 'Not set') . "<br>";

?>