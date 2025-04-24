<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
require_once '../includes/dashboard_header.php';
require_once '../includes/snackbar.php';
require_once '../includes/theme_switcher.php';

$user_id = $_SESSION['user_id'];

// Fetch attempted quizzes
$stmt = $pdo->prepare("SELECT r.id AS result_id, r.score, r.total, r.taken_at, q.title AS quiz_title, q.description AS quiz_description FROM results r JOIN quizzes q ON r.quiz_id = q.id WHERE r.user_id = ? ORDER BY r.taken_at DESC;");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

// data keys - result_id, score, total, taken_at, quiz_title, quiz_description

// Fetch quizzes from DB
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="p-2 min-h-[80vh] my-10">
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-5 text-center">My Quizzes</h2>
    <?php if (count($quizzes) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 px-1">
            <?php foreach ($quizzes as $quiz): ?>
                <div class="max-w-full p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <a href="#">
                        <h5 class="mb-2 text-xl sm:text-2xl  font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($quiz['title']) ?></h5>
                    </a>
                    <p class="mb-3 text-md font-normal text-gray-700 dark:text-gray-400 w-full inline-block truncate overflow-hidden"><?= htmlspecialchars($quiz['description']) ?></p>
                    <!-- Edit quiz -->
                    <a href="manage_quiz.php?id=<?= $quiz['id'] ?>" class="inline-flex items-center px-3 py-2 text-sm sm:text-md font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                    <!-- Delete quiz -->
                    <a href="" onclick="deleteQuiz(<?= $quiz['id'] ?>, this)" class="inline-flex items-center px-3 py-2 text-sm sm:text-md font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <!-- Attempt Quiz -->
                    <a href="quiz/take.php?quiz_id=<?= $quiz['id'] ?>#start" class="float-right inline-flex items-center px-3 py-2 text-sm sm:text-md font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Take Quiz <i class="rtl:rotate-180 text-sm sm:text-md ml-5 fa-solid fa-arrow-right"></i></a>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
            async function deleteQuiz(quizId, btn) {
                const confirmDelete = confirm("Are you sure you want to delete this quiz?");
                if (!confirmDelete) return;
                const res = await fetch('<?= BASE_URL ?>ajax/delete_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        quiz_id: quizId
                    }),
                });
                const data = await res.json();
                if (!data) return showSnackbar('Something went wrong');
                if (data.error) return showSnackbar(data.error)
                if (data.success) {
                    showSnackbar(data.msg);
                    btn.parentElement.parentElement.remove();
                }
            }
        </script>

    <?php else: ?>
        <p class="text-gray-500">No quizzes available</p>
    <?php endif; ?>

    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mt-10 mb-5 text-center">Your Quiz History</h2>
    <?php if (count($results) > 0): ?>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-10">
            <table class="w-full text-md text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Quiz Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            score
                        </th>
                        <th scope="col" class="px-6 py-3">
                            total
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Percentage
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <?= htmlspecialchars($row['quiz_title']) ?>
                            </th>
                            <td class="px-6 py-4">
                                <?= $row['score'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $row['total'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php echo number_format(((int) $row['score'] / (int) $row['total'] * 100), 2) ?>%
                            </td>
                            <td class="px-6 py-4 flex gap-4">
                                <a href="<?= BASE_URL ?>views/quiz/result.php?result_id=<?= $row['result_id'] ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline p-2 px-4 hover:bg-blue-600 hover:text-white rounded-2xl">View</a>
                                <a href="#" onclick="deleteResult(<?= $row['result_id'] ?>, this)" class="font-medium text-red-600 dark:text-red-500 hover:underline p-2 px-4 hover:bg-red-600 hover:text-white rounded-2xl">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
            async function deleteResult(resultId, btn) {
                const confirmDelete = confirm("Are you sure you want to delete this result?");
                if (!confirmDelete) return;
                const res = await fetch('<?= BASE_URL ?>ajax/delete_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        result_id: resultId
                    })
                });
                const data = await res.json();
                if (!data) return showSnackbar('Something went wrong');
                if (data.error) return showSnackbar(data.error)
                if (data.success) {
                    showSnackbar(data.msg);
                    btn.parentElement.parentElement.remove();
                }
            }
        </script>
    <?php else: ?>
        <p class="text-gray-500">You haven’t taken any quizzes yet. 🤔</p>
    <?php endif; ?>

</div>
<?php require_once '../includes/footer.php'; ?>