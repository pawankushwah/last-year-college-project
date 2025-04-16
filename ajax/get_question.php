<?php
require_once '../config/config.php';

$questionId = $_GET['question_id'] ?? null;
if(!$questionId) {
    echo json_encode(['error' => 'Question ID not provided']);
    exit();
}

$stmt = $pdo->prepare("SELECT id, question_text, quiz_id FROM questions WHERE id = ?");
$stmt->execute([$questionId]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$question) {
    echo json_encode(['error' => 'Question not found']);
    exit();
}

$stmtOpt = $pdo->prepare("SELECT option_text, is_correct FROM options WHERE question_id = ?");
$stmtOpt->execute([$question['id']]);
$question['options'] = $stmtOpt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($question);
