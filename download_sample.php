<?php
require_once 'admin_auth.php';
checkAdminAuth();

$filename = 'sample_questions.csv';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, ['Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Answer']);

// Sample data
$sampleQuestions = [
    ['What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Programming Home Page', 'b'],
    ['Which symbol is used for variables in PHP?', '@', '#', '$', '%', 'c'],
    ['What is the correct way to end a PHP statement?', '.', ';', ':', '!', 'b'],
    ['Which function is used to include a file in PHP?', 'include()', 'import()', 'require()', 'Both A and C', 'd'],
    ['What does SQL stand for?', 'Structured Query Language', 'Simple Query Language', 'Standard Query Language', 'System Query Language', 'a']
];

foreach ($sampleQuestions as $question) {
    fputcsv($output, $question);
}

fclose($output);
exit;
?>