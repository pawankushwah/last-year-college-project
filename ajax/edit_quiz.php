<?php
include '../config/config.php';
include '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $title = $data['title'];
    $description = $data['description'];

    if(empty($id) || empty($title) || empty($description)) {
        echo json_encode(['error' => 'Title and description are required.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $quiz = $stmt->fetch();
        if (!$quiz) {
            echo json_encode(['error' => 'Invalid quiz ID']);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }

    echo json_encode(['success' => true, 'msg' => 'Quiz Details updated successfully']);
}