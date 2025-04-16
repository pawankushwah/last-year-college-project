<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $desc  = $_POST['description'];
    $show_on_submit = $_POST['show_on_submit'];

    if (!$title || !$desc || !$show_on_submit) {
        header("Location:". BASE_URL ."views/create_quiz.php");
        exit();
    }
    $show_on_submit = $show_on_submit == "on" ? true : false;

    $stmt = $pdo->prepare("INSERT INTO quizzes (title, description, user_id, show_on_submit) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $desc, $_SESSION['user_id'], $show_on_submit]);

    header("Location:". BASE_URL ."views/manage_quiz.php?id=" . $pdo->lastInsertId());
    exit();
}