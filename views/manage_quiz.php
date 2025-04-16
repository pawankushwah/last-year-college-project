<?php
require_once '../config/config.php';
if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "views/dashboard.php");
    exit();
}

require_once '../includes/functions.php';
redirectIfNotLoggedIn();
require_once '../includes/dashboard_header.php';

$stmt = $pdo->query("SELECT * FROM quizzes WHERE id = " . $_GET['id']);
$quiz = $stmt->fetchAll();
?>

<?php if ($_GET['id']): ?>
    <?php require_once '../includes/snackbar.php'; ?>
    <h2>Manage Quiz</h2>

    <ul>
        <li id="quiz-details">
            <strong><?= htmlspecialchars($quiz[0]['title']) ?></strong><br>
            <p><?= htmlspecialchars($quiz[0]['description']) ?></p><br>
            <span onclick="showEditQuizDetails()">Edit</span>
        </li>
        <li id="quiz-details-edit">

        </li>
        <hr>
    </ul>
    <script>
        const quizDetails = {
            title: '<?= htmlspecialchars($quiz[0]['title']) ?>',
            description: '<?= htmlspecialchars($quiz[0]['description']) ?>'
        };

        async function showEditQuizDetails() {
            const editDetailsContainer = document.getElementById('quiz-details-edit');
            const details = document.getElementById('quiz-details');
            editDetailsContainer.style.display = 'block';
            details.style.display = 'none';
            
            const form = document.createElement('form');
            form.innerHTML = `
                <label>Title:</label><br>
                <input type="text" name="title" value="${quizDetails.title}"><br>
                <label>Description:</label><br>
                <input type="text" name="description" value="${quizDetails.description}"><br>
                <button type="submit">Save</button>
            `;
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(e.target);
                const title = formData.get('title');
                const description = formData.get('description');

                const res = await fetch('<?= BASE_URL ?>ajax/edit_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: <?= $_GET['id'] ?>,
                        title: title,
                        description: description
                    }),
                });
                const result = await res.json();
                if (result.error) {
                    showSnackbar(result.error, 'error');
                    return;
                }
                quizDetails.title = title;
                quizDetails.description = description;
                details.getElementsByTagName('strong')[0].innerHTML = title;
                details.getElementsByTagName('p')[0].innerHTML = description;
                editDetailsContainer.style.display = 'none';
                details.style.display = 'block';
            });
            editDetailsContainer.innerHTML = '';
            editDetailsContainer.appendChild(form);
        }
    </script>

    <button id="add-question-btn">➕ Add Question</button>
    <div id="question-list"></div>

    <!-- Modal for Add/Edit Question -->
    <div id="question-modal" style="display:none; background:#fff; border:1px solid #ccc; padding:20px;">
        <h3 id="modal-title">Add Question</h3>
        <form id="question-form">
            <input type="hidden" name="question_id" id="question_id" value="">
            <label>Question:</label><br>
            <input type="text" name="question_text" id="question_text" required><br><br>

            <span id="add-option-btn">Add Option</span>
            <span id="delete-all-options">delete all Options</span>
            <label>Options:</label><br>
            <div id="options-container">
                <!-- JavaScript will insert option inputs here -->
            </div>
            <br>
            <button type="submit" id="modal-submit-btn">Add Question</button>
            <button type="button" onclick="closeModal()">❌ Cancel</button>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        const modal = document.getElementById('question-modal');
        const form = document.getElementById('question-form');
        let currentOptions = 0;
        const questionList = [];

        loadQuestions(<?= $_GET['id'] ?>);

        function closeModal() {
            modal.style.display = 'none';
            form.reset();
            document.getElementById('options-container').innerHTML = '';
            currentOptions = 0;
        }

        function openModal(question = null) {
            document.getElementById('modal-title').textContent = question ? "Edit Question" : "Add Question";
            document.getElementById('question_id').value = question?.id || '';
            document.getElementById('question_text').value = question?.question_text || '';
            document.getElementById('options-container').innerHTML = '';
            document.getElementById('modal-submit-btn').innerText = question ? "💾 Save" : "Add Question";

            if (question && question.options) {
                question.options.forEach((option, i) => {
                    const optionVal = option.option_text || '';
                    const isChecked = option.is_correct ? "checked" : "";

                    let container = document.createElement('div');
                    container.classList.add('question-option');
                    container.innerHTML = `
                    <input type="radio" name="correct_option" value="${i}" ${isChecked}> 
                    <input type="text" name="options" value="${optionVal}" required><br>
                    <span onclick="removeOption(this)">❌</span>
                `;
                    document.getElementById('options-container').appendChild(container);
                });
            } else {
                for (let i = 0; i < 4; i++) {
                    document.getElementById('add-option-btn').click();
                }
            }
            modal.style.display = 'block';
        }

        // add new option
        document.getElementById('add-option-btn').onclick = (e) => {
            e.preventDefault();
            let container = document.createElement('div');
            container.classList.add('question-option');
            container.innerHTML = `
            <input type="radio" name="correct_option" value="${currentOptions}"> 
            <input type="text" name="options" placeholder="Type option value" required><br>
            <span onclick="removeOption(this)">❌</span>
        `;

            document.getElementById('options-container').appendChild(container);
            currentOptions++;
        }

        function removeOption(btn) {
            btn.parentElement.remove();
            const options = document.querySelectorAll('.question-option');
            options.forEach((option, i) => {
                option.querySelector('input[type="radio"]').value = i;
            });
            currentOptions--;
        }

        // delte all options
        document.getElementById("delete-all-options").onclick = () => {
            document.getElementById('options-container').innerHTML = '';
            currentOptions = 0;
        }

        document.getElementById('add-question-btn').onclick = () => {
            openModal();
        }

        document.getElementById('question-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            if (!formData.get('question_text')) {
                showSnackbar('❌ Question is required');
                return;
            } else if (!formData.getAll('options').length) {
                showSnackbar('❌ Options are required');
                return;
            } else if (!formData.get('correct_option')) {
                showSnackbar('❌ Correct option is required');
                return;
            } else {
                showSnackbar('💾 Saving question...');
            }
            const formObject = Object.fromEntries(formData.entries());
            formObject.options = formData.getAll('options');
            formObject.quiz_id = <?= $_GET['id'] ?>;
            console.log(formObject);

            const res = await fetch('../ajax/save_question.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formObject)
            });
            const result = await res.json();

            if (result.success) {
                closeModal();
                if (!result.data) showSnackbar('Error: Data not found');
                questionList.push(result.data);
                const index = questionList.length - 1;
                appendQuestion(result.data, index);
            } else {
                showSnackbar(`Error saving question: ${result.error}`);
            }
        });

        async function loadQuestions(quizId) {
            const res = await fetch(`../ajax/get_questions.php?quiz_id=${quizId}`);
            const data = await res.json();

            if (!data.length) {
                document.getElementById('question-list').innerHTML = '<strong>No Question Found</strong>';
                return;
            }
            questionList.push(...data);

            data.forEach((ques, index) => {
                appendQuestion(ques, index);
            });
        }

        function editQuestion(quesIndex) {
            const question = questionList[quesIndex];
            openModal(question);
        }

        function appendQuestion(ques, index) {
            const question = JSON.parse(JSON.stringify(ques));
            const questionsContainer = document.getElementById('question-list');
            let container = document.createElement('div');
            container.classList.add('question');
            container.innerHTML = `
                    <strong>${ques.question_text}</strong>
                    <button onclick='editQuestion(${index})'>✏️ Edit</button>
                    <ul>
                        ${ques.options.map(opt => `<li>
                            <input type="checkbox" value="${opt.is_correct}" disabled>
                            <span>${opt.option_text}</span>
                        </li>`).join('')}
                    </ul>
                `;
            document.getElementById('question-list').appendChild(container);
        }
    </script>

<?php endif; ?>