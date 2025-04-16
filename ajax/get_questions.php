<?php
require_once '../config/config.php';

$quiz_id = $_GET['quiz_id'];

$stmt = $pdo->prepare("SELECT id, question_text, quiz_id FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as &$q) {
    $stmtOpt = $pdo->prepare("SELECT id, option_text, is_correct FROM options WHERE question_id = ?");
    $stmtOpt->execute([$q['id']]);
    $q['options'] = $stmtOpt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($questions);
