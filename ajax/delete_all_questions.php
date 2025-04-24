<?php 
include_once '../config/config.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
} else if ($method == "GET") {
    $data = $_GET;
} else {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$quiz_id = $data['quiz_id'];
if (isset($quiz_id) && is_numeric($quiz_id) && is_int($quiz_id + 0) && ((int) $quiz_id) >= 0) {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    echo json_encode(['success' => true, 'msg' => 'All Questions deleted successfully']);
} else {
    echo json_encode(['error' => 'Invalid result ID']);
}