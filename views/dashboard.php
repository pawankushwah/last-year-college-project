<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
require_once '../includes/dashboard_header.php';
require_once '../includes/snackbar.php';

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

<h2>Your Quiz History</h2>

<?php if (count($results) > 0): ?>
    <ul>
        <?php foreach ($results as $row): ?>
            <li>
                <strong><?= htmlspecialchars($row['quiz_title']) ?></strong><br>
                <?= htmlspecialchars($row['quiz_description']) ?><br>
                Score: <?= $row['score'] ?> / <?= $row['total'] ?>
                <br>
                <a href="quiz/result.php?result_id=<?= $row['result_id'] ?>">View Details</a>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>You haven’t taken any quizzes yet.</p>
<?php endif; ?>

<h2>My Quizzes</h2>

<?php if (count($quizzes) > 0): ?>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li>
                <strong><?= htmlspecialchars($quiz['title']) ?></strong><br>
                <?= htmlspecialchars($quiz['description']) ?><br>
                <a href="quiz/take.php?quiz_id=<?= $quiz['id'] ?>">Take Quiz</a>
                <a href="manage_quiz.php?id=<?= $quiz['id'] ?>">Edit Quiz</a>
                <a href="" onclick="deleteQuiz(<?= $quiz['id'] ?>, this)">delete Quiz</a>
                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        async function deleteQuiz(quizId, btn) {
            const confirmDelete = confirm("Are you sure you want to delete this quiz?");
            if (!confirmDelete) return;
            const res = await fetch('<?= BASE_URL?>ajax/delete_quiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ quiz_id: quizId }),
            });
            const data = await res.json();
            if(!data) return showSnackbar('Something went wrong');
            if (data.error) return showSnackbar(data.error)
            if (data.success) showSnackbar(data.msg);
            btn.parentElement.remove();
        }        
    </script>

<?php else: ?>
    <p>No quizzes available at the moment.</p>
<?php endif; ?>
<?php require_once '../includes/footer.php'; ?>
