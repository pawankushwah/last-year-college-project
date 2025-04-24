<?php
require_once '../config/config.php';
$id = $_GET['id'];

if (!is_int($id + 0) || $id <= 0) {
    echo json_encode(['error' => 'Invalid quiz ID']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT title FROM quizzes WHERE id = ?");
    $stmt->execute([$id]);
    $quiz = $stmt->fetch();
    if (!$quiz) {
        echo json_encode(['error' => 'Invalid quiz ID']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

echo json_encode(['success' => true, 'msg' => 'Quiz details fetched successfully', 'data' => $quiz]);
