<?php
include_once '../config/config.php';
include_once '../includes/functions.php';

try {
    // global variable
    $answers = [];

    // get the asnwers from user and the quiz id
    $data = json_decode(file_get_contents("php://input"), true);
    $quiz_id = $data['quiz_id'];
    // checking every question id and value
    foreach ($data as $key => $value) {
        if (strpos($key, 'answer_') !== false) {
            $answer_id = str_replace('answer_', '', $key);
            if (!is_numeric($answer_id) || !is_int($answer_id + 0)) {
                echo json_encode(['error' => 'Invalid answer ID']);
                exit();
            }

            $user_answer = $value;
            if (!is_numeric($user_answer) || !is_int($user_answer + 0)) {
                echo json_encode(['error' => 'Invalid user answer']);
                exit();
            }

            // store the user answer
            $answers[] = [
                'answer_id' => $answer_id,
                'user_answer' => $user_answer
            ];
        }
    }

    // calculate the result
    // get the quiz question
    $stmt = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    if ($stmt->rowCount()) {
        $total_questions = $stmt->rowCount();
    } else {
        echo json_encode(['error' => 'Invalid quiz ID']);
        exit();
    }
    $quizQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $questionIds = [];
    foreach($quizQuestions as $id) {
        $questionIds[] = $id['id'];
    }

    // check the answers
    $score = 0;
    foreach ($answers as $index => $answer) {
        $questionID = $answer['answer_id'];
        $user_answer = $answer['user_answer'];
        if(in_array($questionID, $questionIds)) {
            $stmt = $pdo->prepare("SELECT id FROM options WHERE is_correct = 1 AND question_id = ? AND id = ?");
            $stmt->execute([$questionID, $user_answer]);
            if ($stmt->rowCount() > 0) $score++; // correct answer
        } else {
            $answers[$index]['message'] = "Don't try to put malicious data";
        }
    }

    $isLoggedIn = isLoggedIn();
    $result_id = 0;
    if ($isLoggedIn) {
        // save the result
        $stmt = $pdo->prepare("INSERT INTO results (quiz_id, user_id, score, total, answers_json) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$quiz_id, $_SESSION['user_id'], $score, $total_questions, json_encode($answers)]);
        $result_id = $pdo->lastInsertId();
    }

    $data = [
        'quiz_id' => $quiz_id,
        'is_logged_in' => $isLoggedIn,
        'result_id' => $result_id,
        'score' => $score,
        'total' => $total_questions,
        'answers' => $answers
    ];
    echo json_encode(['success' => true, 'msg' => "You have successfully submitted the quiz", 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
