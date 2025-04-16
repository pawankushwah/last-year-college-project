<?php 
include '../config/config.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

$data = json_decode(file_get_contents("php://input"), true);
$quiz_id = $data['quiz_id'];
if(!is_numeric($quiz_id) || $quiz_id <= 0 || !$quiz_id) {
    echo json_encode(['error' => 'Invalid quiz ID']);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();

if(!$quiz) {
    echo json_encode(['error' => 'Invalid quiz ID']);
    exit();
}

$stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);

echo json_encode(['success' => true, 'msg' => 'Quiz deleted successfully']);