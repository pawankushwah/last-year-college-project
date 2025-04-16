<?php
session_start();
require_once '../config/config.php';

$user_id = $_SESSION['user_id'];
$quiz_id = $_POST['quiz_id'];
$answers = $_POST['answers'];

$score = 0;

foreach ($answers as $question_id => $option_id) {
    $stmt = $pdo->prepare("SELECT * FROM options WHERE id = ? AND is_correct = 1");
    $stmt->execute([$option_id]);

    if ($stmt->rowCount() > 0) {
        $score++;
    }

    // Save user's answers
    $stmt = $pdo->prepare("INSERT INTO user_answers (user_id, quiz_id, question_id, selected_option_id, created_at, updated_at)
                           VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$user_id, $quiz_id, $question_id, $option_id]);
}

echo "<h2>Your score: $score</h2>";
echo "<a href='../views/quiz/list.php'>Back to quizzes</a>";