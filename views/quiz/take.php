<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';
require_once '../../includes/snackbar.php';
require_once '../../includes/theme_switcher.php';

if (!isset($_GET['quiz_id'])) {
    header("Location: " . BASE_URL . "views/quiz/list.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];

$stmt = $pdo->prepare("SELECT q.id AS question_id, q.question_text, o.id AS option_id, o.option_text FROM questions q JOIN options o ON q.id = o.question_id WHERE q.quiz_id = ? ORDER BY q.id, o.id;");
$stmt->execute([$quiz_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$questions = [];
foreach ($rows as $row) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'id' => $qid,
            'question' => $row['question_text'],
            'options' => []
        ];
    }
    $questions[$qid]['options'][] = [
        'id' => $row['option_id'],
        'option_text' => $row['option_text']
    ];
}
?>

<div class="min-h-[100vh] p-2 mt-10">
    <?php if (count($questions) > 0): ?>
        <form id="quiz-form" class="text-sm relative">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

            <div id="questionContainer" class="w-full p-5 pb-20 bg-gray-300 dark:bg-gray-500 rounded-2xl overflow-auto h-[50vh] max-h-[50vh] mb-5 text-lg lg:text-2xl">
                <?php $i = 0; ?>
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question">
                        <span class="flex gap-2 mb-5">
                            <span class="font-bold"><?= ++$i ?>)</span>
                            <span class="font-bold"><?= htmlspecialchars($q['question']) ?></span>
                        </span>
                        <ul class="sm:ml-5 flex flex-col gap-2">
                            <?php foreach ($q['options'] as $opt): ?>
                                <label class="sm:ml-7 fieldset-label">
                                    <input type="radio" name="answer_<?= $q['id'] ?>" value="<?= $opt['id'] ?>" class="checkbox checkbox-accent checkbox-sm mr-2">
                                    <span><?= htmlspecialchars($opt['option_text']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="w-full flex justify-between absolute bottom-28 p-5 lg:hidden">
                <button type="button" class="prevBtn btn btn-sm md:btn-md lg:btn-lg btn-soft">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="hidden">Previous</span>
                </button>
                <button type="button" class="nextBtn btn btn-sm md:btn-md lg:btn-lg btn-soft">
                    <span class="hidden">Next</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <div class="w-full p-5 bg-gray-300 dark:bg-gray-500 rounded-2xl flex justify-between text-xs sm:text-md lg:text-lg">
                <div>
                    <button type="button" onclick="decreaseFontSize()" class="btn btn-xs sm:btn-sm md:btn-md lg:btn-lg btn-soft">
                        <i class="fa-solid fa-font"></i><i class="fas fa-minus"></i>
                    </button>
                    <button type="button" onclick="increaseFontSize()" class="btn btn-xs sm:btn-sm md:btn-md lg:btn-lg btn-soft">
                        <i class="fa-solid fa-font"></i><i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div class="hidden lg:block">
                    <button type="button" class="prevBtn btn btn-lg btn-soft text-lg">
                        <i class="fa-solid fa-arrow-left"></i>
                        Previous
                    </button>
                    <button type="button" class="nextBtn btn btn-lg btn-soft text-lg">
                        Next
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
                <div>
                    <button type="button" onclick="submitQuiz()" class="float-right btn btn-xs sm:btn-sm md:btn-md lg:btn-lg btn-success">Submit Quiz</button>
                </div>
            </div>
        </form>

        <script>
            <?php if (!isLoggedIn()): ?>
                let __username = prompt("Enter Your Name");
            <?php endif; ?>
            document.getElementById('quiz-form').addEventListener('submit', (e) => e.preventDefault());

            async function submitQuiz() {
                const quiForm = document.querySelector('#quiz-form');
                const formData = new FormData(quiForm);

                const res = await fetch('<?= BASE_URL ?>ajax/submit_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData.entries()))
                });
                const data = await res.json();

                if (data.success) {
                    if (!data.data.is_logged_in) {
                        let user_name = __username ? __username : "user" + Math.floor(Math.random() * 9999) + 1000;
                        localStorage.setItem('result_data', JSON.stringify({
                            ...data.data,
                            user_name
                        }));
                    }
                    showSnackbar(data.msg);
                    setTimeout(() => {
                        window.location.href = `<?= BASE_URL ?>views/quiz/result.php?result_id=${data.data.result_id ?? ''}`
                    }, 1000);
                } else {
                    showSnackbar(data.error);
                }
            }
        </script>

        <!-- quiz windows related function -->
        <script>
            function increaseFontSize() {
                const quizWindow = document.querySelector('#questionContainer');
                const currentFontSize = window.getComputedStyle(quizWindow).fontSize;
                const fontSize = parseFloat(currentFontSize) + 1;
                quizWindow.style.fontSize = fontSize + 'px';
            }

            function decreaseFontSize() {
                const quizWindow = document.querySelector('#questionContainer');
                const currentFontSize = window.getComputedStyle(quizWindow).fontSize;
                const fontSize = parseFloat(currentFontSize) - 1;
                quizWindow.style.fontSize = fontSize + 'px';
            }

            // for question navigation
            (() => {
                const questions = Array.from(document.querySelectorAll('.question'));
                const total = questions.length;
                let index = 0;

                const updateButtons = () => {
                    prevBtn.forEach((e) => {
                        e.disabled = index === 0;
                        e.classList.toggle('opacity-50', e.disabled);
                        e.classList.toggle('cursor-not-allowed', e.disabled);
                    });
                    nextBtn.forEach((e) => {
                        e.disabled = index === total - 1;
                        e.classList.toggle('opacity-50', e.disabled);
                        e.classList.toggle('cursor-not-allowed', e.disabled);
                    });
                };

                const show = i => {
                    questions.forEach(q => q.classList.add('hidden'));
                    questions[i]?.classList.remove('hidden');
                    updateButtons();
                };

                const navigate = dir => {
                    index = Math.min(Math.max(index + dir, 0), total - 1);
                    show(index);
                };

                const prevBtn = document.querySelectorAll('.prevBtn');
                const nextBtn = document.querySelectorAll('.nextBtn');
                prevBtn.forEach((e) => {
                    e.onclick = () => navigate(-1);
                })
                nextBtn.forEach((e) => {
                    e.onclick = () => navigate(1);
                })

                show(index); // Initial show
            })();
        </script>
    <?php else: ?>
        <div class="text-center">
            <h2 class="text-2xl font-bold p-10">Congruatulations! You have scored 100 out of 100 😄</h2>
            <div>😅 Reason: This quiz don't have any Question</div>
        </div>

    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>