<?php
if (isset($_GET['result_id']) && is_numeric($_GET['result_id']) && is_int($_GET['result_id'] + 0) && ((int) $_GET['result_id']) >= 0) {
    // continue execution
} else {
    echo "Invalid Request";
    exit();
}

$result_id = $_GET['result_id'];

include_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';
require_once '../../includes/snackbar.php';

if($result_id) {
    // fetch result + quiz details + user details
    $stmt = $pdo->prepare("SELECT r.score, r.total, r.taken_at, r.answers_json, u.name AS user_name, q.id AS quiz_id, q.title AS quiz_title FROM results r JOIN users u ON r.user_id = u.id JOIN quizzes q ON r.quiz_id = q.id WHERE r.id = ? ORDER BY r.taken_at DESC;");
    $stmt->execute([$result_id]);
    $results = $stmt->fetchAll()[0];
    
    // data keys - score, total, taken_at, answers_json, user_name, quiz_id, quiz_title
} else {
    $results = [
        'score' => 0,
        'total' => 100,
        'taken_at' => '',
        'user_name' => '',
        'quiz_id' => '',
        'quiz_title' => '',
    ];
}
?>

<p>
<h1 id="quiz_title"><?= $results['quiz_title'] ?></h1>
<a id="attempt_quiz_btn" href="<?= BASE_URL ?>views/quiz/take.php?quiz_id=<?= $results['quiz_id'] ?>">Attempt this Quiz</a>
</p>
<p>Name:
    <span id="user_name"><?= $results['user_name'] ?></span>
</p>
<p>Score:
    <span id="score"><?= $results['score'] ?></span>
</p>
<p>Total:
    <span id="total"><?= $results['total'] ?></span>
</p>
<p>Percentage:
    <span id="percentage"><?php echo number_format(((int) $results['score'] / (int) $results['total'] * 100), 2) ?>%</span>
</p>
<span onclick="showAnswers()">Show answers</span>
<div id="questions_container"></div>

<script>
    let data = {};
    <?php if (!$result_id): ?>
        data = JSON.parse(localStorage.getItem('result_data'));
        document.getElementById("user_name").innerText = data.user_name;
        document.getElementById("score").innerText = data.score;
        document.getElementById("total").innerText = data.total;
        document.getElementById("percentage").innerText = (data.score / data.total * 100).toFixed(2) + "%";
        const attempt_quiz_btn = document.getElementById("attempt_quiz_btn");
        attempt_quiz_btn.href = attempt_quiz_btn.href + data.quiz_id;
    <?php else: ?>
        data.answers = JSON.parse(<?php echo json_encode($results['answers_json']); ?>);
    <?php endif; ?>

    let quiz_id = <?php echo '"' . $results['quiz_id'] .'"' ?>;
    async function getQuestions() {
        if(!quiz_id) quiz_id = data.quiz_id;
        const res = await fetch(`<?= BASE_URL ?>ajax/get_questions.php?quiz_id=${quiz_id}`);
        const json = await res.json();
        console.log(json);
        return json;
    }

    let questions;
    async function showAnswers(){
        if(!questions) questions = await getQuestions();
        const questions_container = document.getElementById("questions_container");
        questions.forEach((q, i) => {
            let userAnswer = data.answers.filter((a)=> a.answer_id == q.id)[0]["user_answer"];
            console.log(userAnswer);

            const question = document.createElement("div");
            question.classList.add("question");
            question.innerHTML = `
                <h3>${q.question_text}</h3>
                ${q.options.map(({id, option_text, is_correct}) => {
                    let style = (is_correct) ? "correct" : (userAnswer == id) ? "incorrect" : "";
                    return `<div class="option ${style}">
                        ${option_text}
                    </div>`;
                }).join("")}
            `;
            questions_container.appendChild(question);
        });
    }


</script>

<?php require_once '../../includes/footer.php'; ?>