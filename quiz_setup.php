<?php
require_once 'config.php';
session_start();

if (!isset($_GET['quiz_id']) || !isset($_GET['user_name'])) {
    header('Location: index.php');
    exit;
}

$quiz_id = (int)$_GET['quiz_id'];
$user_name = $_GET['user_name'];
$selected_question_count = isset($_GET['question_count']) ? (int)$_GET['question_count'] : null;

// Get quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND is_active = 1");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    header('Location: index.php');
    exit;
}

// Get total question count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$total_questions = $stmt->fetchColumn();

if ($total_questions == 0) {
    header('Location: index.php');
    exit;
}

// Determine how many questions to use
$questions_to_use = $total_questions;

if ($quiz['allow_question_selection'] && $selected_question_count) {
    $questions_to_use = min($selected_question_count, $total_questions);
} elseif ($quiz['questions_to_show']) {
    $questions_to_use = min($quiz['questions_to_show'], $total_questions);
}

// Get all questions and randomly select the required number
$sql = "SELECT * FROM questions WHERE quiz_id = ?";
if ($quiz['shuffle_questions']) {
    $sql .= " ORDER BY RAND()";
} else {
    $sql .= " ORDER BY id";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz_id]);
$all_questions = $stmt->fetchAll();

// Select random questions if needed
if ($questions_to_use < count($all_questions)) {
    if (!$quiz['shuffle_questions']) {
        // If not already shuffled, shuffle now for random selection
        shuffle($all_questions);
    }
    $selected_questions = array_slice($all_questions, 0, $questions_to_use);
} else {
    $selected_questions = $all_questions;
}

// Clear any existing session data
unset($_SESSION['attempt_id']);
unset($_SESSION['quiz_id']);
unset($_SESSION['current_question']);
unset($_SESSION['answers']);
unset($_SESSION['questions']);

// Create quiz attempt
$stmt = $pdo->prepare("INSERT INTO quiz_attempts (quiz_id, user_name, total_questions, selected_questions_count, attempted_questions, correct_answers, score) VALUES (?, ?, ?, ?, 0, 0, 0)");
$stmt->execute([$quiz_id, $user_name, count($selected_questions), $questions_to_use]);
$attempt_id = $pdo->lastInsertId();

// Set up session
$_SESSION['attempt_id'] = $attempt_id;
$_SESSION['quiz_id'] = $quiz_id;
$_SESSION['current_question'] = 0;
$_SESSION['answers'] = array_fill(0, count($selected_questions), null);
$_SESSION['questions'] = $selected_questions;

// Redirect to quiz
header('Location: take_quiz.php?quiz_id=' . $quiz_id . '&user_name=' . urlencode($user_name));
exit;
?>