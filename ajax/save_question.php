<?php
require_once '../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);
// assuming, not a malicious user
$quiz_id = $data['quiz_id'];
$question_id = $data['question_id'] ?? null;
$question_text = $data['question_text'];
$options = $data['options'];
$correct_index = $data['correct_option'];

if (!$quiz_id || !$question_text || !$options || !$correct_index) {
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$msg = '';

try {
    if ($question_id) {
        // Update question
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
        $stmt->execute([$question_text, $question_id]);
    
        $stmt = $pdo->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->execute([$question_id]);
        $msg = 'Question updated successfully';
    } else {
        // Insert new question
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
        $stmt->execute([$quiz_id, $question_text]);
        $question_id = $pdo->lastInsertId();
        $msg = 'Question added successfully';
    }
    
    $new_options_obj = [];
    foreach ($options as $index => $opt) {
        $is_correct = ($index == $correct_index) ? 1 : 0;
        $new_options_obj[] = ['option_text' => $opt, 'is_correct' => $is_correct];
        $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
        $stmt->execute([$question_id, $opt, $is_correct]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    $data = [
        'question_id' => $question_id,
        'question_text' => $question_text,
        'options' => $new_options_obj,
        'correct_option' => $correct_index,
        'quiz_id' => $quiz_id
    ];
    
    echo json_encode(['success' => true, 'msg' => $msg, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
