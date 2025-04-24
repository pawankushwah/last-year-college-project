<?php
// Fetch quizzes from DB
$stmt = $pdo->prepare("SELECT * FROM quizzes ORDER BY created_at DESC");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/theme_switcher.php';
?>

<div class="p-2 min-h-[80vh] my-10">
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-10 text-center">Available Quizzes</h2>

    <?php if (count($quizzes) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 px-1">
            <?php foreach ($quizzes as $quiz): ?>
                <!-- Quiz Card -->
                <div class="max-w-full p-6 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 text-center hover:bg-gray-100 dark:hover:bg-gray-900">
                    <a href="<?= BASE_URL ?>views/quiz/take.php?quiz_id=<?= $quiz['id'] ?>">
                        <h5 class="mb-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($quiz['title']) ?></h5>
                    </a>
                    <p class="mb-3 text-md font-normal text-gray-700 dark:text-gray-400 w-full inline-block truncate overflow-hidden"><?= htmlspecialchars($quiz['description']) ?></p>
                    <a href="<?= BASE_URL ?>views/quiz/take.php?quiz_id=<?= $quiz['id'] ?>#start" class="inline-flex items-center px-3 py-2 text-sm sm:text-md font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Take Quiz
                        <i class="rtl:rotate-180 text-sm sm:text-md ml-5 fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No quizzes available at the moment.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>