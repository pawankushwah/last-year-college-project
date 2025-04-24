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

$result_id = $data['result_id'];
if (isset($result_id) && is_numeric($result_id) && is_int($result_id + 0) && ((int) $result_id) >= 0) {
    $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
    $stmt->execute([$result_id]);
    echo json_encode(['success' => true, 'msg' => 'Result deleted successfully']);
} else {
    echo json_encode(['error' => 'Invalid result ID']);
}