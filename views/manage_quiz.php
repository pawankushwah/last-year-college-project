<?php
require_once '../config/config.php';
if (!isset($_GET['id'])) {
    // validate quiz id
    $id = (int) $_GET['id'];
    if ($id <= 0) {
        header("Location: " . BASE_URL . "views/dashboard.php");
        exit();
    }
}

require_once '../includes/functions.php';
redirectIfNotLoggedIn();

$stmt = $pdo->query("SELECT * FROM quizzes WHERE id = " . $_GET['id']);
$quiz = $stmt->fetchAll();
?>

<?php if ($_GET['id']): ?>
    <?php
    require_once '../includes/dashboard_header.php';
    require_once '../includes/theme_switcher.php';
    require_once '../includes/snackbar.php';
    ?>

    <div class="p-2 min-h-[70vh] mt-5">
        <ul class="m-auto flex flex-col md:flex-row gap-2">
            <li class="relative border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 rounded-2xl p-5 mb-2 w-full">
                <div id="quiz-details">
                    <h1 id="quiz-title" class="text-lg sm:text-xl lg:text-2xl font-bold"><?= htmlspecialchars($quiz[0]['title']) ?></h1>
                    <span onclick="showEditQuizDetails()" class="absolute top-2 right-2 btn btn-info btn-xs sm:btn-sm">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </span>
                    <span class="flex text-sm sm:text-lg lg:text-xl text-gray-500">
                        <span id="quiz-description" class="inline-block truncate w-72 overflow-hidden"><?= htmlspecialchars($quiz[0]['description']) ?></span>
                    </span>
                </div>
                <div id="quiz-details-edit"></div>
            </li>
            <li id="quiz-options" class="border-2 border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 rounded-2xl p-5 mb-2 w-full">
                <div class="flex flex-wrap gap-2">
                    <button id="add-question-btn" onclick="openModal()" title="Add Question" class="btn btn-xs sm:btn-sm lg:btn-lg btn-outline dark:btn-dash btn-secondary"><i class="fa-solid fa-plus"></i> Add</button>
                    <button id="generate-question-btn" title="Generate Questions" onclick="openGenerateModal()" class="btn btn-xs sm:btn-sm lg:btn-lg btn-outline dark:btn-dash btn-accent"><i class="fa-solid fa-robot"></i>Generate</button>
                    <button id="update-question-json-btn" title="Update Questions Through JSON" onclick="openJSONImportModal()" class="btn btn-xs sm:btn-sm lg:btn-lg btn-outline"><i class="fa-brands fa-node-js"></i> Update </button>
                    <button id="add-question-json-btn" title="Add New Questions Through JSON" onclick="openJSONImportModal(true)" class="btn btn-xs sm:btn-sm lg:btn-lg btn-outline"><i class="fa-brands fa-node-js"></i> Add</button>
                    <div>
                        <button id="download-json-btn" title="Download Quiz JSON" onclick="downloadJSON()" class="btn btn-xs sm:btn-sm lg:btn-lg btn-warning"><i class="fa-solid fa-download"></i> </button>
                        <button id="save-all-btn" title="Save All Unsaved Questions" onclick="saveAllUnsavedQuestions()" class="btn btn-xs sm:btn-sm lg:btn-lg btn-primary"><i class="fa-solid fa-save"></i> </button>
                        <button id="delete-all-btn" title="Delete All Questions" onclick="deleteAllQuestions()" class="btn btn-xs sm:btn-sm lg:btn-lg btn-error"><i class="fa-solid fa-trash"></i> </button>
                    </div>
                    <a href="<?= BASE_URL ?>views/quiz/take.php?quiz_id=<?= $_GET['id'] ?>#start" id="attempt-quiz-btn" title="Add New Questions Through JSON" class="btn btn-xs sm:btn-sm lg:btn-lg btn-outline btn-primary">Attempt Quiz <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </li>
        </ul>
        <div class="my-3">
            <!-- Questions container -->
            <div id="question-list" class="my-10 flex flex-col gap-5 text-xs sm:text-sm md:text-md lg:text-lg"></div>

            <!-- Modal for Generate Questions -->
            <div id="generate-modal" class="fixed top-0 left-0 w-full h-full justify-center bg-gray-800/95 py-10 pb-20 px-10 xl:px-60 overflow-auto hidden">
                <div class="border-box bg-white dark:bg-gray-800 flex flex-col rounded-2xl h-fit mt-2 p-5 gap-2">
                    <h1 class="text-2xl font-bold text-center mb-2">Generate Question</h1>
                    <label class="input w-full">
                        <input type="text" name="topic" id="topic" required placeholder="Type your Topic" class="grow">
                    </label>

                    <label class="input w-full">
                        <input type="number" name="count" value="1" id="count" required placeholder="Number of Questions" class="grow">
                    </label>

                    <button type="button" onclick="generateQuestions()" class="btn btn-primary">Generate</button>
                </div>
            </div>

            <!-- Modal for Add/Edit Question -->
            <div id="question-modal" class="fixed top-0 left-0 w-full h-full justify-center bg-gray-800/95 hidden">
                <div class="bg-white dark:bg-gray-800 p-5 flex flex-col rounded-2xl w-lg h-fit mt-2">
                    <h3 id="modal-title" class="text-2xl font-bold mb-2 text-center">Add Question</h3>
                    <form id="question-form" class="text-center flex flex-col gap-2 border-2 border-gray-300 rounded-2xl p-5">
                        <input type="hidden" name="question_id" id="question_id" value="">

                        <label class="input w-full">
                            <input type="text" name="question_text" id="question_text" required placeholder="Type your Question" class="grow">
                        </label>

                        <div class="flex gap-2 w-full">
                            <span id="add-option-btn" class="btn btn-outline btn-primary w-1/2">Add Option</span>
                            <span id="delete-all-options" class="btn btn-outline btn-primary w-1/2">Delete all Options</span>
                        </div>

                        <div id="options-container">
                            <!-- JavaScript will insert option inputs here -->
                        </div>
                        <br>
                        <div class="flex gap-2">
                            <button type="button" onclick="closeModal()" class="btn btn-outline btn-error w-1/2">❌ Cancel</button>
                            <button type="submit" id="modal-submit-btn" class="btn btn-primary w-1/2">Add Question</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal for JSON import -->
            <div id="json-import-modal" class="fixed top-0 left-0 w-full h-full justify-center bg-gray-800/95 py-10 pb-20 px-10 xl:px-60 overflow-auto hidden">
                <div class="border-box bg-white dark:bg-gray-800 flex flex-col rounded-2xl w-full h-fit mt-2 p-5">
                    <h1 class="text-2xl font-bold text-center mb-2">Import JSON</h1>
                    <div class="flex justify-between">
                        <div class="flex justify-center items-center gap-2">
                            <input type="range" id="font-size-slider" min="10" max="30" value="14" class="range range-xs">
                        </div>
                        <div class="">
                            <button type="button" onclick="editor.refresh()" class="cursor-pointer btn btn-soft">
                                <i class="fa-solid fa-refresh"></i>
                            </button>
                            <button type="button" onclick="saveQuestionJSON()" class="btn btn-soft btn-primary">
                                <i class="fa-solid fa-save"></i>
                                Save Data
                            </button>
                        </div>
                    </div>
                    <textarea name="json" id="json-editor"></textarea>
                    <div class="w-full">
                        <div role="alert" id="json-error-container" class="alert alert-error my-5 hidden">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span id="json-error"></span>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.15/lib/codemirror.css">
                <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.15/lib/codemirror.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.15/mode/javascript/javascript.js"></script>
                <!-- JSONLint Parser -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jsonlint/1.6.0/jsonlint.min.js"></script>
            </div>

            <!-- JSON editor script -->
            <script>
                const editor = CodeMirror.fromTextArea(document.getElementById("json-editor"), {
                    lineNumbers: true,
                    theme: "default",
                    mode: "application/json"
                });

                const errorBox = document.getElementById("json-error");
                const errorBoxContainer = document.getElementById("json-error-container");
                let currentMarker = null;

                editor.on("change", () => {
                    const code = editor.getValue();

                    // Remove old marker
                    if (currentMarker) {
                        currentMarker.clear();
                        currentMarker = null;
                    }
                    try {
                        jsonlint.parse(code); // Use JSONLint for accurate line/column
                        errorBox.textContent = "";
                        errorBoxContainer.classList.add('hidden');
                    } catch (e) {
                        errorBox.textContent = "JSON Error: " + e.message;
                        errorBoxContainer.classList.remove('hidden');

                        // Parse error line
                        const match = e.message.match(/line (\d+)/i);
                        if (match) {
                            const lineNum = parseInt(match[1], 10) - 1;
                            const lineLen = editor.getLine(lineNum).length;

                            currentMarker = editor.markText({
                                line: lineNum,
                                ch: 0
                            }, {
                                line: lineNum,
                                ch: lineLen
                            }, {
                                className: "cm-error-line"
                            });
                        }
                    }
                });
            </script>
            <style>
                .CodeMirror {
                    height: 500px;
                    width: 100%;
                    font-size: 12px;
                }

                .cm-error-line {
                    background-color: rgba(255, 0, 0, 0.1);
                    border-left: 4px solid red;
                }
            </style>

            <script>
                const slider = document.getElementById("font-size-slider");
                slider.addEventListener("input", () => {
                    const fontSize = slider.value + "px";
                    editor.getWrapperElement().style.fontSize = fontSize;
                    editor.refresh();
                });
            </script>

            <script>
                const quizDetails = {
                    title: `<?= htmlspecialchars($quiz[0]['title']) ?>`,
                    description: `<?= htmlspecialchars($quiz[0]['description']) ?>`
                };

                async function showEditQuizDetails() {
                    const editDetailsContainer = document.getElementById('quiz-details-edit');
                    const details = document.getElementById('quiz-details');
                    const title = document.getElementById('quiz-title');
                    const description = document.getElementById('quiz-description');
                    editDetailsContainer.style.display = 'block';
                    details.style.display = 'none';

                    const form = document.createElement('form');
                    form.innerHTML = `
                    <label class="input w-full">
                        <input type="text" name="title" value="${quizDetails.title}" placeholder="Type your Title" class="grow">
                    </label>

                    <label class="fieldset">
                        <textarea name="description" rows="4" cols="40" placeholder="Type your Description" required class="textarea h-24 w-full">${quizDetails.description}</textarea>
                    </label>
                    <button type="submit" class="btn btn-outline btn-info w-full">Save</button>
                `;
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        const _title = formData.get('title');
                        const _description = formData.get('description');

                        const res = await fetch('<?= BASE_URL ?>ajax/edit_quiz.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: `<?= $_GET['id'] ?>`,
                                title: _title,
                                description: _description
                            }),
                        });
                        const result = await res.json();
                        if (result.error) {
                            showSnackbar(result.error);
                            return;
                        }
                        showSnackbar(result.msg);
                        quizDetails.title = _title;
                        quizDetails.description = _description;
                        title.innerText = _title;
                        description.innerText = _description;
                        editDetailsContainer.style.display = 'none';
                        details.style.display = 'block';
                    });
                    editDetailsContainer.innerHTML = '';
                    editDetailsContainer.appendChild(form);
                }
            </script>
        </div>

        <?php require_once '../includes/footer.php'; ?>

        <script>
            const modal = document.getElementById('question-modal');
            const form = document.getElementById('question-form');
            let currentOptions = 0;
            let questionList = [];
            let currentEditIndex = -1;

            loadQuestions(<?= $_GET['id'] ?>);

            function closeModal() {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                form.reset();
                document.getElementById('options-container').innerHTML = '';
                currentOptions = 0;
            }

            function openModal(question = null, index = null) {
                document.getElementById('modal-title').textContent = question ? "Edit Question" : "Add Question";
                document.getElementById('question_id').value = question?.id || '';
                document.getElementById('question_text').value = question?.question_text || '';
                document.getElementById('options-container').innerHTML = '';
                document.getElementById('modal-submit-btn').innerText = question ? "💾 Save" : "Add Question";

                if (question && question.options) {
                    question.options.forEach((option) => {
                        const optionId = option.id || '';
                        const optionVal = option.option_text || '';
                        const isChecked = option.is_correct ? "checked" : "";

                        let container = document.createElement('div');
                        container.classList.add('question-option');
                        container.innerHTML = `
                        <label class="input w-full mt-3">
                            <span>
                                <input type="radio" name="correct_option" value="${optionId}" ${isChecked} class="radio radio-sm"> 
                            </span>
                            <input type="text" name="options" value="${optionVal}" placeholder="Type option value" required class="grow"><br>
                            <span onclick="removeOption(this)" class="cursor-pointer">
                                <i class="fa-solid fa-trash text-red-500"></i>
                            </span>
                        </label>
                        `;
                        document.getElementById('options-container').appendChild(container);
                    });
                } else {
                    for (let i = 0; i < 4; i++) {
                        document.getElementById('add-option-btn').click();
                    }
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            // listener for closing modal 
            const modalOverlay = document.getElementById('question-modal');
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    closeModal();
                }
            });

            // add new option
            document.getElementById('add-option-btn').onclick = (e) => {
                e.preventDefault();
                let container = document.createElement('div');
                container.classList.add('question-option');
                container.innerHTML = `
                <label class="input w-full mt-3">
                    <span>
                        <input type="radio" name="correct_option" value="${currentOptions}" class="radio radio-sm"> 
                    </span>
                    <input type="text" name="options" placeholder="Type option value" required class="grow"><br>
                    <span onclick="removeOption(this)" class="cursor-pointer">
                        <i class="fa-solid fa-trash text-red-500"></i>
                    </span>
                </label>
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

            // delete all options
            document.getElementById("delete-all-options").onclick = () => {
                document.getElementById('options-container').innerHTML = '';
                currentOptions = 0;
            }

            // JSON import modal
            function openJSONImportModal(newQuestions = false) {
                const modal = document.getElementById("json-import-modal");
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                if (newQuestions) {
                    let code = [{
                        "question_text": "",
                        "options": [{
                                "option_text": "",
                                "is_correct": 0
                            },
                            {
                                "option_text": "",
                                "is_correct": 0
                            },
                            {
                                "option_text": "",
                                "is_correct": 0
                            },
                            {
                                "option_text": "",
                                "is_correct": 0
                            }
                        ]
                    }];
                    editor.setValue(JSON.stringify(code, null, 2));
                } else editor.setValue(JSON.stringify(questionList, null, 2));
            }

            function closeJSONImportModal() {
                const modal = document.getElementById("json-import-modal");
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // listener for closing JSON import modal 
            const JSONImportModal = document.getElementById('json-import-modal');
            JSONImportModal.addEventListener('click', (e) => {
                if (e.target === JSONImportModal) {
                    closeJSONImportModal();
                }
            });

            // Generate Question Modal
            function openGenerateModal() {
                const modal = document.getElementById("generate-modal");
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeGenerateModal() {
                const modal = document.getElementById("generate-modal");
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            const generateModal = document.getElementById('generate-modal');
            generateModal.addEventListener('click', (e) => {
                if (e.target === generateModal) {
                    closeGenerateModal();
                }
            });

            async function generateQuestions() {
                showSnackbar('⌛ Generating questions...');
                setTimeout(() => showSnackbar('⌛ Please Wait Patiently, It may take a while...'), 3000);

                const topic = document.getElementById('topic').value;
                const count = document.getElementById('count').value;
                const res = await fetch(`<?= BASE_URL ?>ajax/generate.php?type=ques&quiz_id=<?= $_GET['id'] ?>&prompt=${topic}&count=${count}`);
                const json = await res.json();

                if (json.error) return showSnackbar(json.error);
                json.data.forEach((question) => {
                    question['is_generated'] = true;
                    question['is_saved'] = false;
                    question['quiz_id'] = <?= $_GET['id'] ?>;
                    questionList.length === 0 ? document.getElementById('question-list').innerHTML = '' : null;
                    questionList.push(question);
                    appendQuestion(question, questionList.length - 1);
                });

                closeGenerateModal();
                showSnackbar('✅ Questions Generated');
            }

            // save question
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
                formObject.options.forEach((option, i) => {
                    const id = questionList[currentEditIndex].options[i].id;
                    formObject.options[i] = {
                        id: id,
                        option_text: option,
                        is_correct: id == formObject.correct_option ? 1 : 0
                    };
                })
                formObject.quiz_id = <?= $_GET['id'] ?>;

                const data = {
                    id: formObject.question_id,
                    question_text: formObject.question_text,
                    options: formObject.options
                }

                const res = await fetch('<?= BASE_URL ?>ajax/save_questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: <?= $_GET['id'] ?>,
                        data: [data]
                    })
                });
                const result = await res.json();

                if (result.success) {
                    closeModal();
                    if (!result.data) showSnackbar('Error: Data not found');
                    questionList[currentEditIndex] = result.data[0];
                    if (questionList.length === 0) document.getElementById('question-list').innerHTML = "";
                    refreshQuestionList();
                } else {
                    showSnackbar(`Error saving question: ${result.error}`);
                }
            });

            // save generated questions
            async function saveQuestion(index) {
                let question = questionList[index];
                let data = {
                    question_text: question.question_text,
                    options: question.options
                }

                const res = await fetch('<?= BASE_URL ?>ajax/save_questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: <?= $_GET['id'] ?>,
                        data: [data]
                    })
                });
                const result = await res.json();

                if (result.success) {
                    if (!result.data) showSnackbar('Error: Data not found');
                    questionList[index] = result.data[0];
                    refreshQuestionList();
                } else {
                    showSnackbar(`Error saving question: ${result.error}`);
                }
            }

            async function saveQuestionJSON(json = null) {
                let code = json ?? editor.getValue();
                if(typeof code !== 'string') code = JSON.stringify(code);
                const res = await fetch('<?= BASE_URL ?>ajax/save_questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: <?= $_GET['id'] ?>,
                        data: JSON.parse(code)
                    })
                });
                const result = await res.json();

                if (result.success) {
                    if (!result.data) showSnackbar('Error: Data not found');
                    closeJSONImportModal();
                    showSnackbar(result.msg);
                    questionList = result.data;
                    refreshQuestionList();
                } else {
                    showSnackbar(`Error saving question: ${result.error}`);
                }
            }

            async function deleteQuestion(quesIndex) {
                const question = questionList[quesIndex];
                if (question.is_generated) {
                    questionList.splice(quesIndex, 1);
                    refreshQuestionList();
                    return;
                }
                const res = await fetch('<?= BASE_URL ?>ajax/delete_question.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: question.id,
                        quiz_id: <?php echo $_GET['id']; ?>
                    })
                });
                const result = await res.json();
                if (result.success) {
                    questionList.splice(quesIndex, 1);
                    refreshQuestionList();
                    showSnackbar(result.msg);
                } else {
                    showSnackbar(`Error deleting question: ${result.error}`);
                }
            }

            async function loadQuestions(quizId) {
                const res = await fetch(`../ajax/get_questions.php?quiz_id=${quizId}`);
                const data = await res.json();

                if (!data.length) {
                    document.getElementById('question-list').innerHTML = '<div class="font-bold text-xl text-center">Add Your First Question</div>';
                    return;
                }
                questionList.push(...data);

                data.forEach((ques, index) => {
                    appendQuestion(ques, index);
                });
            }

            function refreshQuestionList() {
                const questionsContainer = document.getElementById('question-list');
                questionsContainer.innerHTML = '';
                if(questionList.length === 0) {
                    questionsContainer.innerHTML = '<div class="font-bold text-xl text-center">Add Your First Question</div>';
                    return;
                }
                questionList.forEach((ques, index) => {
                    appendQuestion(ques, index);
                });
            }

            function editQuestion(quesIndex) {
                const question = questionList[quesIndex];
                currentEditIndex = quesIndex;
                openModal(question);
            }

            function downloadJSON() {
                const data = questionList.map(ques => {
                    return {
                        question_text: ques.question_text,
                        options: ques.options.map(opt => {
                            return {
                                option_text: opt.option_text,
                                is_correct: opt.is_correct
                            }
                        })
                    }
                });
                const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
                const downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                const fileName = document.getElementById('quiz-title').innerText + ".json";
                downloadAnchorNode.setAttribute("download", fileName);
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
            }

            async function saveAllUnsavedQuestions() {
                const unsavedQuestions = questionList.filter(ques => ques.is_saved === false);
                await saveQuestionJSON(unsavedQuestions);
            }

            async function deleteAllQuestions() {
                const prompt = confirm('Are you sure you want to delete all questions?');
                if (!prompt) return;
                const nonGeneratedQuestions = questionList.filter(ques => !ques.is_generated);
                if (nonGeneratedQuestions.length === 0) {
                    questionList = [];
                    refreshQuestionList();
                    return;
                }
                const res = await fetch('<?= BASE_URL ?>ajax/delete_all_questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: <?= $_GET['id'] ?>
                    })
                });
                const result = await res.json();

                if (result.success) {
                    if (!result.data) showSnackbar('Error: Data not found');
                    showSnackbar(result.msg);
                    questionList = [];
                    refreshQuestionList();
                } else {
                    showSnackbar(`Error saving question: ${result.error}`);
                }
            }

            function appendQuestion(ques, index) {
                const question = JSON.parse(JSON.stringify(ques));
                const questionsContainer = document.getElementById('question-list');
                let container = document.createElement('div');
                container.classList.add('question');
                container.innerHTML = `
                    <div class="relative ${ques.is_generated ? 'generated' : ''} bg-yellow-100 dark:bg-gray-700 p-5 pt-10 rounded-2xl">
                        <span class="flex gap-2">
                            <span class="font-bold">${index + 1})</span>
                            <span class="font-bold">${ques.question_text}</span>
                            <span class="flex gap-2 ml-auto w-fit h-fit absolute right-2 top-2">
                                <button onclick='editQuestion(${index})' class="group cursor-pointer p-1 px-2 rounded-xl bg-gray-200 hover:text-white hover:bg-blue-500"><i class="fa-solid fa-pen text-blue-500 group-hover:text-white"></i></button>
                                <button onclick='deleteQuestion(${index})' class="group cursor-pointer p-1 px-2 rounded-xl bg-gray-200 hover:text-white hover:bg-blue-500"><i class="fa-solid fa-trash text-red-500 group-hover:text-white"></i></button>
                                ${
                                    ques.is_generated ? `<button onclick='saveQuestion(${index})' class="group cursor-pointer p-1 px-2 rounded-xl bg-gray-200 hover:text-white hover:bg-violet-500"><i class="fa-solid fa-save text-violet-500 group-hover:text-white"></i></button>` : ''
                                }
                            </span>
                        </span>
                        <ul class="ml-7">
                            ${ques.options.map(opt => `<li class="${opt.is_correct ? "bg-green-300 dark:bg-green-600" : "" } p-1 px-2 mt-2 rounded-xl w-fit">
                                <input type="checkbox" ${opt.is_correct ? "checked" : ""} disabled class="checkbox checkbox-success checkbox-sm mr-2">
                                <span>${opt.option_text}</span>
                            </li>`).join('')}
                        </ul>
                    </div>
                `;
                document.getElementById('question-list').appendChild(container);
            }
        </script>
    </div>
<?php endif; ?>