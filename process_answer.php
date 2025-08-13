<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['attempt_id'])) {
    header('Location: index.php');
    exit;
}

$question_id = $_POST['question_id'];
$attempt_id = $_POST['attempt_id'];
$action = $_POST['action'];
$selected_answer = isset($_POST['answer']) ? $_POST['answer'] : null;
$question_index = (int)$_POST['question_index'];
$quiz_id = $_POST['quiz_id'];
$user_name = $_POST['user_name'];

// Initialize answers array if not exists
if (!isset($_SESSION['answers'])) {
    $_SESSION['answers'] = array_fill(0, count($_SESSION['questions']), null);
}

// Debug logging
error_log("Action: $action, Question Index: $question_index, Selected Answer: " . ($selected_answer ?? 'NULL'));

// Handle different actions
switch ($action) {
    case 'save':
        // Just save the answer, don't move
        $_SESSION['answers'][$question_index] = $selected_answer;
        break;
        
    case 'next':
        // Save answer and move to next question
        $_SESSION['answers'][$question_index] = $selected_answer;
        $_SESSION['current_question']++;
        break;
        
    case 'previous':
        // Save current answer and move to previous question
        if ($selected_answer) {
            $_SESSION['answers'][$question_index] = $selected_answer;
        }
        if ($_SESSION['current_question'] > 0) {
            $_SESSION['current_question']--;
        }
        break;
        
    case 'skip':
        // Mark as skipped and move to next question
        $_SESSION['answers'][$question_index] = 'SKIPPED';
        $_SESSION['current_question']++;
        break;
        
    case 'navigate':
        // Save current answer if provided and navigate to specific question
        if ($selected_answer) {
            $_SESSION['answers'][$question_index] = $selected_answer;
        }
        if (isset($_POST['goto_question'])) {
            $target_question = (int)$_POST['goto_question'];
            if ($target_question >= 0 && $target_question < count($_SESSION['questions'])) {
                $_SESSION['current_question'] = $target_question;
            }
        }
        break;
        
    case 'finish':
        // Save current answer if provided
        if ($selected_answer) {
            $_SESSION['answers'][$question_index] = $selected_answer;
        } elseif (!isset($_SESSION['answers'][$question_index])) {
            $_SESSION['answers'][$question_index] = 'SKIPPED';
        }
        
        // Process all answers and finish quiz
        processAllAnswers();
        header('Location: finish_quiz.php');
        exit;
}

// Debug: Log current session state
error_log("Current answers: " . json_encode($_SESSION['answers']));
error_log("Current question: " . $_SESSION['current_question']);

// Redirect back to quiz with proper parameters
header('Location: take_quiz.php?quiz_id=' . $quiz_id . '&user_name=' . urlencode($user_name));
exit;

function processAllAnswers() {
    global $pdo, $attempt_id;
    
    $correct_count = 0;
    $attempted_count = 0;
    
    error_log("Processing answers: " . json_encode($_SESSION['answers']));
    error_log("Total questions in session: " . count($_SESSION['questions']));
    
    // Process each question
    for ($i = 0; $i < count($_SESSION['questions']); $i++) {
        $question = $_SESSION['questions'][$i];
        $selected_answer = $_SESSION['answers'][$i] ?? 'SKIPPED';
        
        error_log("Question $i: Selected = $selected_answer, Correct = " . $question['correct_answer']);
        
        $is_correct = false;
        $answer_to_store = null;
        
        if ($selected_answer && $selected_answer !== 'SKIPPED') {
            $attempted_count++;
            $is_correct = ($selected_answer === $question['correct_answer']);
            if ($is_correct) $correct_count++;
            $answer_to_store = $selected_answer;
        }
        
        // Save individual answer to database
        try {
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
            
            error_log("Saved answer for question " . $question['id'] . ": answer=$answer_to_store, correct=" . ($is_correct ? 1 : 0));
            
        } catch (Exception $e) {
            error_log("Error saving answer: " . $e->getMessage());
        }
    }
    
    // Update attempt with final counts
    try {
        $stmt = $pdo->prepare("UPDATE quiz_attempts SET attempted_questions = ?, correct_answers = ? WHERE id = ?");
        $stmt->execute([$attempted_count, $correct_count, $attempt_id]);
        
        error_log("Updated attempt: attempted=$attempted_count, correct=$correct_count");
        
    } catch (Exception $e) {
        error_log("Error updating attempt: " . $e->getMessage());
    }
}
?>