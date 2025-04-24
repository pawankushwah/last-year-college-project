<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method']);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION['user_id'];

    $json = $data['data'] ?? null;
    if (!$json || !is_array($json)) {
        echo json_encode(['error' => 'Missing required fields or Invalid data']);
        exit();
    }

    $quiz_id = $data['quiz_id'] ?? null;
    if (!$quiz_id || !is_numeric($quiz_id) || !is_int($quiz_id + 0)) {
        echo json_encode(['error' => 'Invalid Quiz ID']);
        exit();
    }
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE id = ? AND user_id = ?");
    $stmt->execute([$quiz_id, $user_id]);
    $quiz = $stmt->fetch();
    if (!$quiz) {
        echo json_encode(['error' => 'Invalid Quiz ID']);
        exit();
    }

    // validating the data
    foreach ($json as $index => $ques) {
        $question_id = $ques['id'] ?? null;
        $question_text = $ques['question_text'] ?? null;
        $options = $ques['options'] ?? null;

        if (!$question_text || !$options) {
            echo json_encode(['error' => 'Missing required fields in question ' . ($index + 1)]);
            exit();
        }

        if ($question_id && !is_numeric($question_id + 0) && !is_int($question_id + 0)) {
            echo json_encode(['error' => 'Invalid question id in question ' . ($index + 1)]);
            exit();
        }

        if (!is_string($question_text)) {
            echo json_encode(['error' => 'Invalid question text in question ' . ($index + 1)]);
            exit();
        }

        if (!is_array($options)) {
            echo json_encode(['error' => 'Invalid options in question ' . ($index + 1)]);
            exit();
        }

        if (count($options) < 2) {
            echo json_encode(['error' => 'At least two options are required in question ' . ($index + 1)]);
            exit();
        } else if (count($options) > 5) {
            echo json_encode(['error' => 'Maximum of 5 options are allowed in question ' . ($index + 1)]);
            exit();
        }

        foreach ($options as $key => $value) {
            if (!is_array($value)) {
                echo json_encode(['error' => 'Invalid options in question ' . ($index + 1)]);
                exit();
            }
            $id = $value['id'] ?? null;
            $opt = $value['option_text'] ?? null;
            $is_correct = $value['is_correct'] ?? null;
            if (!isset($value['option_text']) || !is_string($opt) || !isset($value['is_correct'])) {
                echo json_encode(['error' => 'Missing required fields in option ' . ($index + 1)]);
                exit();
            }
            if ($id && !is_numeric($id + 0) && !is_int($id + 0)) {
                echo json_encode(['error' => 'Invalid option id in option ' . ($index + 1)]);
                exit();
            }
            if (!in_array($is_correct, [0, 1], true)) {
                echo json_encode(['error' => 'Invalid is_correct value in option ' . ($index + 1)]);
                exit();
            }
        }
    }

    // updating and adding the data into the database
    $data = []; // question_id, question_text, options, correct_option
    foreach ($json as $index => $ques) {
        $question_id = $ques['id'] ?? null;
        $question_text = $ques['question_text'];
        $options = $ques['options'];
        $availableOptions = [];

        if ($question_id) {
            // check question_id exist to user or not
            $stmt = $pdo->prepare("SELECT q.id FROM questions q JOIN quizzes z ON q.quiz_id = z.id WHERE q.id = ? AND z.user_id = ?");
            $stmt->execute([$question_id, $user_id]);
            if (!$stmt->rowCount()) {
                echo json_encode(['error' => 'Invalid question id in question ' . ($index + 1)]);
                exit();
            }

            // get all the options of the question
            $stmt = $pdo->prepare("SELECT id FROM options WHERE question_id = ?");
            $stmt->execute([$question_id]);
            $availableOptions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), "id");
            $availableOptionsCount = count($options);

            // check the user given options
            $toBeAdded = 0;
            foreach ($options as $optIndex => $opt) {
                $id = $opt['id'] ?? null;

                if ($id && !in_array($id, $availableOptions)) {
                    echo json_encode(['error' => 'Invalid Option ID in ' . ($optIndex + 1) . ' Option of ' . $index + 1 . ' Question']);
                    exit();
                } else if(!$id) $toBeAdded++;
            }

            if ($toBeAdded >  5 - $availableOptionsCount) {
                echo json_encode(['error' => 'You can\'t add more than 5 Questions']);
                exit();
            } else if ($availableOptionsCount + $toBeAdded < 2) {
                echo json_encode(['error' => 'Atleast 2 Options are required']);
                exit();
            }

            // Update question
            $stmt = $pdo->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
            $stmt->execute([$question_text, $question_id]);
        } else {
            // Insert new question
            $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
            $stmt->execute([$quiz_id, $question_text]);
            $question_id = $pdo->lastInsertId();
        }

        $new_options_obj = [];
        foreach ($options as $index => $opt) {
            $id = $opt['id'] ?? null;
            $text = $opt['option_text'];
            $is_correct = $opt['is_correct'];

            if ($id) {
                // update options table
                $stmt = $pdo->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ?");
                $stmt->execute([$text, $is_correct, $id]);
            } else {
                // Insert new option
                $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->execute([$question_id, $text, $is_correct]);
                $id = $pdo->lastInsertId();
            }
            $new_options_obj[] = ['id' => $id, 'option_text' => $text, 'is_correct' => $is_correct];
        }

        $data[] = [
            'id' => $question_id,
            'question_text' => $question_text,
            'options' => $new_options_obj
        ];
    }

    echo json_encode(['success' => true, 'msg' => "Questions saved successfully", 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
