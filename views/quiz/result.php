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
require_once '../../includes/theme_switcher.php';

if ($result_id) {
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
<div class="p-2 min-h-[70vh] my-10">
    <h1 id="quiz_title" class="text-xl sm:text-2xl lg:text-3xl font-bold text-center mb-5 bg-yellow-300 dark:bg-yellow-600 p-2 w-fit m-auto rounded-2xl"><?= $results['quiz_title'] ?></h1>
    <div class="relative overflow-x-auto w-fit m-auto rounded-2xl">
        <table class="w-full text-md text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <tbody>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Name
                    </th>
                    <td class="px-6 py-4">
                        <span id="user_name"><?= $results['user_name'] ?></span>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Score
                    </th>
                    <td class="px-6 py-4">
                        <span id="score"><?= $results['score'] ?></span>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Total
                    </th>
                    <td class="px-6 py-4">
                        <span id="total"><?= $results['total'] ?></span>
                    </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Percentage
                    </th>
                    <td class="px-6 py-4">
                        <span id="percentage"><?php echo number_format(((int) $results['score'] / (int) $results['total'] * 100), 2) ?>%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="m-auto w-fit flex flex-wrap justify-center gap-4 mt-5">
        <a id="attempt_quiz_btn" href="<?= BASE_URL ?>views/quiz/take.php?quiz_id=<?= $results['quiz_id'] ?>#start" class="btn btn-soft btn-secondary">Attempt this Quiz</a>
        <span onclick="showAnswers()" class="btn btn-outline btn-primary">Show answers</span>
        <span onclick="navigator.clipboard.writeText(window.location.href);showSnackbar('Copied to clipboard')" title="Copy to clipboard" class="btn btn-outline btn-success"><i class="fa-solid fa-share-alt"></i></span>
    </div>
    <div id="questions_container" class="my-10 flex flex-col gap-2"></div>
</div>
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

    let quiz_id = <?php echo '"' . $results['quiz_id'] . '"' ?>;
    async function getQuestions() {
        if (!quiz_id) quiz_id = data.quiz_id;
        const res = await fetch(`<?= BASE_URL ?>ajax/get_questions.php?quiz_id=${quiz_id}`);
        const json = await res.json();
        return json;
    }

    <?php if (!$result_id): ?>
        // get Quiz Details And Update them in DOM
        (async () => {
            quiz_id = data.quiz_id;
            let res = await fetch(`<?= BASE_URL ?>ajax/get_quiz_details.php?id=${quiz_id}`);
            let json = await res.json();
            document.getElementById("quiz_title").innerText = json.data.title;
        })();
    <?php endif; ?>

    let questions;
    async function showAnswers() {
        if (!questions) questions = await getQuestions();
        const questions_container = document.getElementById("questions_container");
        questions.forEach((q, i) => {
            let userAnswer = data.answers.filter((a) => a.answer_id == q.id)[0]?.user_answer;
            const question = document.createElement("div");
            question.classList.add("question");
            question.innerHTML = `
                <div class="bg-yellow-100 dark:bg-gray-700 p-5 rounded-2xl">
                    <span class="flex gap-2">
                        <span class="text-lg font-bold">${i + 1})</span>
                        <span class="text-lg font-bold">${q.question_text}</span>
                    </span>
                    <ul class="ml-7">
                        ${q.options.map(opt => {
                            let style = (opt.is_correct) ? "bg-green-300 dark:bg-green-600" : (userAnswer == opt.id) ? "bg-red-300 dark:bg-red-600" : "";
                            return `<li class="${style} p-1 px-2 mt-2 rounded-xl w-fit">
                                <input type="checkbox" ${userAnswer == opt.id ? "checked" : ""} disabled class="checkbox checkbox-neutral checkbox-sm mr-2">
                                <span>${opt.option_text}</span>
                            </li>`;
                        }).join('')}
                    </ul>
                </div>
            `;
            questions_container.appendChild(question);
        });
    }
</script>

<?php require_once '../../includes/footer.php'; ?>