<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
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

// validate the inputs
$type = $data['type']; // ques, title, desc, title_desc
$quiz_id = $data['quiz_id'] ?? ''; // validate quiz id only if type is ques
$count = $data['count'] ?? ''; // validate count only if type is ques
$prompt = $data['prompt'];

if (!$type || !in_array($type, ['ques', 'title', 'desc', 'title_desc'])) {
    echo json_encode(['error' => 'Invalid Type']);
    exit();
}

if (!$prompt || !is_string($prompt)) {
    echo json_encode(['error' => 'Invalid Prompt']);
    exit();    
}
if($method == "GET") $prompt = urldecode($prompt);

// includes gemini functions
require_once '../includes/gemini.php';

if ($data['type'] == 'ques') {
    // validate quiz id
    if (!$quiz_id || !is_numeric($quiz_id) || !is_int($quiz_id + 0)) {
        echo json_encode(['error' => 'Invalid Quiz ID']);
        exit();
    }

    // validate count
    if (!$count || !is_numeric($count) || !is_int($count + 0)) {
        echo json_encode(['error' => 'Invalid Count']);
        exit();
    }

    // check if the user is the owner of the quiz
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE id = ? AND user_id = ?");
    $stmt->execute([$quiz_id, $_SESSION['user_id']]);
    $quiz = $stmt->fetch();
    if (!$quiz) {
        echo json_encode(['error' => 'Invalid Quiz ID']);
        exit();
    }
    // generate the question
    $response = generateQuestion(GEMINI_API, GEMINI_MODEL, $prompt, $count);
    $resText = $response['candidates'][0]['content']['parts'][0]['text'];
    $questions = [];

    try {
        // Step 1: Remove the wrapping ```json and ```
        $cleaned = trim($resText, "\"");
        $cleaned = preg_replace('/^```json\n|```$/', '', $cleaned);
        
        // Step 2: Decode escaped characters
        $unescaped = stripcslashes($cleaned);
        $questions = json_decode($unescaped, true);
    } catch (Exception $e) {
        echo json_encode(['error' => "Error generating question: Invalid Response from AI"]);
        exit();
    }

    echo json_encode(['success' => true, 'msg' => 'Question generated successfully', 'data' => $questions]);
} else {
    echo json_encode(["error" => "coming soon...."]);
}