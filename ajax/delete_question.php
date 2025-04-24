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

try {
    // validate the inputs
    $qid = $data['id'];
    if (!$qid || !is_numeric($qid) || !is_int($qid + 0)) {
        echo json_encode(['error' => 'Invalid Question ID']);
        exit();
    }

    $quiz_id = $data['quiz_id'];
    if (!$quiz_id || !is_numeric($quiz_id) || !is_int($quiz_id + 0)) {
        echo json_encode(['error' => 'Invalid Quiz ID']);
        exit();
    }

    // delete the question
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?");
    $stmt->execute([$qid, $quiz_id]);
    if($stmt->rowCount() == 0) {
        echo json_encode(['error' => 'Invalid Question ID']);
        exit();
    }
    echo json_encode(['success' => true, 'msg' => 'Question deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Something Went Wrong: ' . $e->getMessage()]);
    exit();
}
