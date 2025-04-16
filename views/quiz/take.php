<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';
require_once '../../includes/snackbar.php';

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
<?php if(count($questions) > 0): ?>
<h2>Take Quiz</h2>
<pre>
</pre>

<form id="quiz-form">
    <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">    

    <?php $i = 0; ?>
    <?php foreach ($questions as $q): ?>
        <div>
            <p><strong><?= (++$i) . ". " . htmlspecialchars($q['question']) ?></strong></p>
            <?php foreach ($q['options'] as $opt): ?>
                <label>
                    <input type="radio" name="answer_<?= $q['id'] ?>" value="<?= $opt['id'] ?>">
                    <span><?= htmlspecialchars($opt['option_text']) ?></span>   
                </label><br>
            <?php endforeach; ?>
        </div>
        <br>
    <?php endforeach; ?>

    <button type="button" onclick="submitQuiz()">Submit Quiz</button>
</form>

<script>
    <?php if(!isLoggedIn()): ?>
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
            if (!data.data.is_logged_in){
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
<?php else: ?>
    <div>
        <h2>Congruatulations! You have scored 100 out of 100 😄</h2>
        <div>😅 Reason: This quiz don't have any Question</div>
    </div>

<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>